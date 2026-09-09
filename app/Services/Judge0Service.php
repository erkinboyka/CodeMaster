<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Judge0Service
{
    protected string $apiUrl;
    protected string $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.judge0.url', 'https://judge0-ce.p.rapidapi.com');
        $this->apiToken = config('services.judge0.token', '');
    }

    public function submitAndWait(array $payload): array
    {
        $headers = [];
        if ($this->apiToken) {
            if (str_contains($this->apiUrl, 'rapidapi.com')) {
                $headers['X-RapidAPI-Key'] = $this->apiToken;
                $headers['X-RapidAPI-Host'] = 'judge0-ce.p.rapidapi.com';
            } else {
                $headers['X-Auth-Token'] = $this->apiToken;
            }
        }

        $response = Http::withHeaders($headers)->timeout(30)->post("{$this->apiUrl}/submissions?base64_encoded=false&wait=true", $payload);

        if ($response->successful()) {
            return $response->json();
        }

        Log::error('Judge0 API error', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return [
            'stdout' => null,
            'stderr' => 'Execution service temporarily unavailable.',
            'status' => ['description' => 'Error'],
        ];
    }

    public function resolveLanguageId(string $lang): int
    {
        $languages = [
            'javascript' => 63,
            'js' => 63,
            'python' => 71,
            'py' => 71,
            'java' => 62,
            'cpp' => 54,
            'c++' => 54,
            'c' => 50,
            'php' => 68,
            'ruby' => 72,
            'go' => 60,
            'rust' => 73,
            'typescript' => 74,
            'ts' => 74,
            'sql' => 82,
            'mysql' => 82,
            'postgresql' => 82,
            'html' => 61,
            'css' => 61,
        ];

        $normalized = $this->normalizeLanguage($lang);

        return $languages[$normalized] ?? 63;
    }

    public function normalizeLanguage(string $lang): string
    {
        return strtolower(trim($lang));
    }

    public function runPractice(string $lang, string $code, array $tests, ?string $functionName = null): array
    {
        $normalized = $this->normalizeLanguage($lang);

        if (in_array($normalized, ['sql', 'mysql', 'postgresql'])) {
            return $this->runSqlLocal($code, $tests);
        }

        if (empty($tests)) {
            return [
                'status' => 'wrong_answer',
                'results' => [],
                'total_tests' => 0,
                'passed_tests' => 0,
            ];
        }

        // Function-style задачи (def twoSum(...) / function twoSum...):
        // оборачиваем код пользователя драйвером, который парсит stdin,
        // вызывает функцию и печатает нормализованный результат.
        $driverCode = $this->buildDriver($normalized, $code, $functionName);

        $languageId = $this->resolveLanguageId($lang);
        $results = [];

        foreach ($tests as $index => $test) {
            $payload = [
                'source_code' => $driverCode,
                'language_id' => $languageId,
                'stdin' => $test['input'] ?? '',
                'expected_output' => $test['expected'] ?? $test['output'] ?? '',
            ];

            $result = $this->submitAndWait($payload);

            $passed = isset($result['stdout']) &&
                trim($result['stdout']) === trim($test['expected'] ?? $test['output'] ?? '');

            $rawMemory = $result['memory'] ?? null;
            $memoryMb = $rawMemory !== null ? round((float)$rawMemory / 1024, 2) : null;

            $results[] = [
                'test_case' => $index + 1,
                'description' => $test['description'] ?? 'Test ' . ($index + 1),
                'passed' => $passed,
                'input' => $test['input'] ?? '',
                'expected' => $test['expected'] ?? $test['output'] ?? '',
                'output' => $result['stdout'] ?? '',
                'error' => $result['stderr'] ?? null,
                'time' => $result['time'] ?? null,
                'memory' => $memoryMb,
            ];
        }

        $allPassed = collect($results)->every('passed');

        return [
            'status' => $allPassed ? 'accepted' : 'wrong_answer',
            'results' => $results,
            'total_tests' => count($tests),
            'passed_tests' => collect($results)->where('passed', true)->count(),
        ];
    }

    /**
     * Строит исполняемый код: пользовательский код + драйвер вызова функции.
     * Драйвер добавляется только для python/javascript при известном имени функции,
     * иначе код исполняется как есть (stdin/stdout контракт).
     */
    protected function buildDriver(string $normalizedLang, string $code, ?string $functionName): string
    {
        $functionName = trim((string) $functionName);
        if ($functionName === '' || !preg_match('/^[A-Za-z_]\w*$/', $functionName)) {
            return $code;
        }

        if ($normalizedLang === 'python' || $normalizedLang === 'py') {
            $driver = <<<'PYDRIVER'
_CM_IMPORT_SYS = __import__('sys')
_CM_IMPORT_JSON = __import__('json')
_CM_IMPORT_AST = __import__('ast')
def _cm_split(s):
    parts, cur, depth, instr, q = [], '', 0, False, ''
    for ch in s:
        if instr:
            cur += ch
            if ch == q:
                instr = False
        elif ch == '"' or ch == "'":
            instr, q, cur = True, ch, cur + ch
        elif ch in '[{(':
            depth += 1
            cur += ch
        elif ch in ']})':
            depth -= 1
            cur += ch
        elif ch == ',' and depth == 0:
            parts.append(cur)
            cur = ''
        else:
            cur += ch
    parts.append(cur)
    return parts
def _cm_parse(s):
    s = s.strip()
    try:
        return _CM_IMPORT_AST.literal_eval(s)
    except Exception:
        return s
def _cm_norm(v):
    if v is True:
        return 'true'
    if v is False:
        return 'false'
    if v is None:
        return 'null'
    if isinstance(v, (list, tuple, dict)):
        try:
            return _CM_IMPORT_JSON.dumps(list(v) if isinstance(v, tuple) else v, separators=(',', ':'))
        except Exception:
            return str(v)
    return str(v)
_cm_raw = _CM_IMPORT_SYS.stdin.read().strip()
_cm_args = [_cm_parse(p) for p in _cm_split(_cm_raw)]
_cm_res = CMFUNC(*_cm_args)
if _cm_res is None and _cm_args:
    _cm_res = _cm_args[0]
print(_cm_norm(_cm_res))
PYDRIVER;
            return rtrim($code) . "\n" . str_replace('CMFUNC', $functionName, $driver);
        }

        if ($normalizedLang === 'javascript' || $normalizedLang === 'js') {
            $driver = <<<'JSDRIVER'
const _cmFs = require('fs');
function _cmSplit(s){const parts=[];let cur='',depth=0,inS=false,q='';for(const ch of s){if(inS){cur+=ch;if(ch===q)inS=false}else if(ch==='"'||ch==="'"){inS=true;q=ch;cur+=ch}else if(ch==='['||ch==='{'||ch==='('){depth++;cur+=ch}else if(ch===']'||ch==='}'||ch===')'){depth--;cur+=ch}else if(ch===','&&depth===0){parts.push(cur);cur=''}else{cur+=ch}}parts.push(cur);return parts}
function _cmParse(s){s=s.trim();try{return JSON.parse(s)}catch(e){if((s.startsWith('"')&&s.endsWith('"'))||(s.startsWith("'")&&s.endsWith("'")))return s.slice(1,-1);const n=Number(s);return isNaN(n)?s:n}}
function _cmNorm(v){if(v===true)return 'true';if(v===false)return 'false';if(v==null)return 'null';if(typeof v==='object')return JSON.stringify(v);return String(v)}
const _cmRaw=_cmFs.readFileSync(0,'utf8').trim();
const _cmArgs=_cmSplit(_cmRaw).map(_cmParse);
let _cmRes=CMFUNC(..._cmArgs);
if(_cmRes===undefined&&_cmArgs.length)_cmRes=_cmArgs[0];
console.log(_cmNorm(_cmRes));
JSDRIVER;
            return rtrim($code) . "\n" . str_replace('CMFUNC', $functionName, $driver);
        }

        return $code;
    }

    public function runSqlPractice(string $engine, string $code, array $tests): array
    {
        $results = [];

        foreach ($tests as $index => $test) {
            $payload = [
                'source_code' => $code,
                'language_id' => $this->resolveLanguageId('sql'),
                'stdin' => json_encode([
                    'query' => $test['query'] ?? $code,
                    'expected' => $test['expected'] ?? '',
                ]),
            ];

            $result = $this->submitAndWait($payload);

            $passed = isset($result['stdout']) &&
                trim($result['stdout']) === trim($test['expected'] ?? '');

            $results[] = [
                'test_case' => $index + 1,
                'passed' => $passed,
                'expected' => $test['expected'] ?? '',
                'output' => $result['stdout'] ?? '',
                'error' => $result['stderr'] ?? null,
            ];
        }

        return [
            'status' => collect($results)->every('passed') ? 'accepted' : 'wrong_answer',
            'results' => $results,
            'total_tests' => count($tests),
            'passed_tests' => collect($results)->where('passed', true)->count(),
        ];
    }

    public function runSqlLocal(string $code, array $tests): array
    {
        $results = [];
        $normalizedCode = preg_replace('/\s+/', ' ', strtolower(trim($code)));

        foreach ($tests as $index => $test) {
            $input = strtolower(trim($test['input'] ?? ''));
            $expected = strtolower(trim($test['expected'] ?? ''));
            $passed = false;

            if (str_contains($input, 'select запрос') || str_contains($input, 'select')) {
                $selectCount = preg_match_all('/\bselect\b/', $normalizedCode);
                $hasWhere = str_contains($normalizedCode, 'where');
                $hasFrom = str_contains($normalizedCode, 'from');
                $passed = $selectCount >= 4 && $hasWhere && $hasFrom;

            } elseif (str_contains($input, 'create table') || str_contains($input, 'создание таблицы')) {
                $passed = str_contains($normalizedCode, 'create table');

            } elseif (str_contains($input, 'show tables') || str_contains($input, 'таблица')) {
                $passed = str_contains($normalizedCode, 'create table') ||
                          str_contains($normalizedCode, 'show tables') ||
                          str_contains($normalizedCode, 'select');

            } elseif (str_contains($input, 'insert') || str_contains($input, 'вставка')) {
                $passed = str_contains($normalizedCode, 'insert into');

            } elseif (str_contains($input, 'update')) {
                $passed = str_contains($normalizedCode, 'update') && str_contains($normalizedCode, 'set');

            } elseif (str_contains($input, 'delete')) {
                $passed = str_contains($normalizedCode, 'delete from');

            } elseif (str_contains($input, 'join')) {
                $passed = str_contains($normalizedCode, 'join');

            } elseif (str_contains($input, 'left join')) {
                $passed = str_contains($normalizedCode, 'left join');

            } elseif (str_contains($input, 'index') || str_contains($input, 'индекс')) {
                $passed = str_contains($normalizedCode, 'create index') || str_contains($normalizedCode, 'create unique index');

            } elseif (str_contains($input, 'view') || str_contains($input, 'представление')) {
                $passed = str_contains($normalizedCode, 'create view');

            } elseif (str_contains($input, 'alter')) {
                $passed = str_contains($normalizedCode, 'alter table');

            } elseif (str_contains($input, 'drop')) {
                $passed = str_contains($normalizedCode, 'drop table') || str_contains($normalizedCode, 'drop database');

            } elseif (str_contains($input, 'where')) {
                $passed = str_contains($normalizedCode, 'where');

            } elseif (str_contains($input, 'group by')) {
                $passed = str_contains($normalizedCode, 'group by');

            } elseif (str_contains($input, 'order by')) {
                $passed = str_contains($normalizedCode, 'order by');

            } elseif (str_contains($input, 'having')) {
                $passed = str_contains($normalizedCode, 'having');

            } elseif (str_contains($input, 'union')) {
                $passed = str_contains($normalizedCode, 'union');

            } elseif (str_contains($input, 'subquery') || str_contains($input, 'подзапрос')) {
                $passed = preg_match('/select.*select/s', $normalizedCode) ||
                          str_contains($normalizedCode, 'in (') ||
                          str_contains($normalizedCode, 'exists');

            } elseif (str_contains($input, 'transaction') || str_contains($input, 'транзакц')) {
                $passed = str_contains($normalizedCode, 'begin') ||
                          str_contains($normalizedCode, 'commit') ||
                          str_contains($normalizedCode, 'start transaction');

            } else {
                $keywords = array_filter(explode(' ', $input), fn($w) => strlen($w) > 2);
                $found = 0;
                foreach ($keywords as $kw) {
                    if (str_contains($normalizedCode, $kw)) $found++;
                }
                $passed = count($keywords) > 0 && ($found / count($keywords)) >= 0.5;
            }

            $results[] = [
                'test_case' => $index + 1,
                'description' => $test['description'] ?? $test['input'] ?? 'Test ' . ($index + 1),
                'passed' => $passed,
                'input' => $test['input'] ?? '',
                'expected' => $test['expected'] ?? '',
                'output' => $passed ? $test['expected'] ?? '' : '',
                'error' => null,
                'time' => null,
                'memory' => null,
            ];
        }

        return [
            'status' => collect($results)->every('passed') ? 'accepted' : 'wrong_answer',
            'results' => $results,
            'total_tests' => count($tests),
            'passed_tests' => collect($results)->where('passed', true)->count(),
        ];
    }

    public function runFillPractice(array $tests, array $answers): array
    {
        $results = [];

        foreach ($tests as $index => $test) {
            $userAnswer = $answers[$index] ?? '';
            $correct = strtolower(trim($userAnswer)) === strtolower(trim($test['answer'] ?? ''));

            $results[] = [
                'test_case' => $index + 1,
                'passed' => $correct,
                'expected' => $test['answer'] ?? '',
                'user_answer' => $userAnswer,
            ];
        }

        return [
            'status' => collect($results)->every('passed') ? 'accepted' : 'wrong_answer',
            'results' => $results,
            'total_tests' => count($tests),
            'passed_tests' => collect($results)->where('passed', true)->count(),
        ];
    }
}
