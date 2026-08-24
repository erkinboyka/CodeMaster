const fs = require('fs');

function extractKeys(content) {
  const lines = content.split('\n');
  const keys = {};
  for (const line of lines) {
    let match = line.match(/^\s*'(peer[._][^']*)'\s*=>\s*'(.+?)',?\s*$/);
    if (match) {
      keys[match[1]] = match[2];
      continue;
    }
    match = line.match(/^\s*'(peer_error)'\s*=>\s*'(.+?)',?\s*$/);
    if (match) {
      keys[match[1]] = match[2];
      continue;
    }
    match = line.match(/^\s*'(interview_[^']*)'\s*=>\s*'(.+?)',?\s*$/);
    if (match) {
      keys[match[1]] = match[2];
      continue;
    }
  }
  return keys;
}

const tgPhp = fs.readFileSync('C:/OSPanel/home/Codemaster/lang/tg.php', 'utf8');
const ruPhp = fs.readFileSync('C:/OSPanel/home/Codemaster/lang/ru.php', 'utf8');

const tgKeys = extractKeys(tgPhp);
const ruKeys = extractKeys(ruPhp);

const tgJson = JSON.parse(fs.readFileSync('C:/OSPanel/home/Codemaster/lang/tg.json', 'utf8'));
const ruJson = JSON.parse(fs.readFileSync('C:/OSPanel/home/Codemaster/lang/ru.json', 'utf8'));

let tgMissing = 0;
let ruMissing = 0;

for (const k of Object.keys(tgKeys)) {
  if (!(k in tgJson)) {
    tgMissing++;
  }
}
for (const k of Object.keys(ruKeys)) {
  if (!(k in ruJson)) {
    ruMissing++;
  }
}

console.log('TG total peer+interview keys:', Object.keys(tgKeys).length);
console.log('TG missing:', tgMissing);
console.log('RU total peer+interview keys:', Object.keys(ruKeys).length);
console.log('RU missing:', ruMissing);

// Add missing keys to tg.json
for (const [k, v] of Object.entries(tgKeys)) {
  if (!(k in tgJson)) {
    tgJson[k] = v;
  }
}

// Add missing keys to ru.json
for (const [k, v] of Object.entries(ruKeys)) {
  if (!(k in ruJson)) {
    ruJson[k] = v;
  }
}

fs.writeFileSync('C:/OSPanel/home/Codemaster/lang/tg.json', JSON.stringify(tgJson, null, 4) + '\n', 'utf8');
fs.writeFileSync('C:/OSPanel/home/Codemaster/lang/ru.json', JSON.stringify(ruJson, null, 4) + '\n', 'utf8');

console.log('Done! Files updated.');
