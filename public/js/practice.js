document.addEventListener('DOMContentLoaded', function() {
    var starterCodeEl = document.getElementById('starter-code-data');
    var starterCode = starterCodeEl ? starterCodeEl.textContent : '';

    var languageModes = {
        'python': 'python',
        'javascript': 'javascript',
        'typescript': 'text/typescript',
        'php': 'text/x-php',
        'html': 'htmlmixed',
        'yaml': 'yaml',
        'json': { name: 'javascript', json: true },
        'text': 'text/plain',
        'sql': 'text/x-sql',
        'mysql': 'text/x-sql',
        'c': 'text/x-csrc',
        'cpp': 'text/x-c++src',
        'java': 'text/x-java',
        'ruby': 'text/x-ruby',
        'go': 'text/x-go',
        'csharp': 'text/x-csharp',
    };

    var defaultCodes = {
        'python': '# Write your code here\n\ndef main():\n    pass\n\nif __name__ == "__main__":\n    main()\n',
        'javascript': '// Write your code here\n\nfunction main() {\n    \n}\n\nmain();\n',
        'typescript': '// Write your code here\n\nfunction main(): void {\n    \n}\n\nmain();\n',
        'php': '<?php\n\n// Write your code here\n\n',
        'html': '<!DOCTYPE html>\n<html lang="ru">\n<head>\n    <meta charset="UTF-8">\n    <title>My Page</title>\n</head>\n<body>\n    <!-- Your code here -->\n</body>\n</html>',
        'sql': '-- Write your SELECT/INSERT/UPDATE queries here\n\n',
        'mysql': '-- Write your MySQL queries here\n\n',
        'c': '#include <stdio.h>\n\nint main() {\n    // Write your code here\n    return 0;\n}\n',
        'cpp': '#include <iostream>\nusing namespace std;\n\nint main() {\n    // Write your code here\n    return 0;\n}\n',
        'java': 'public class Main {\n    public static void main(String[] args) {\n        // Write your code here\n    }\n}\n',
        'ruby': '# Write your code here\n\ndef main\n    \nend\n\nmain\n',
        'go': 'package main\n\nimport "fmt"\n\nfunc main() {\n    // Write your code here\n}\n',
        'csharp': 'using System;\n\nclass Program {\n    static void Main() {\n        // Write your code here\n    }\n}\n',
    };

    var initLang = document.querySelector('[x-data]')?.__x?.$data?.language || 'python';
    try {
        var scope = Alpine.$data(document.querySelector('[x-data]'));
        initLang = scope.language || 'python';
    } catch(e) {
        initLang = 'python';
    }

    var editor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
        mode: languageModes[initLang] || 'python',
        theme: 'dracula',
        lineNumbers: true,
        indentUnit: 4,
        tabSize: 4,
        indentWithTabs: false,
        matchBrackets: true,
        autoCloseBrackets: true,
        autoCloseTags: true,
        lineWrapping: true,
    });
    editor.setValue(starterCode || defaultCodes[initLang] || defaultCodes['python']);
    editor.refresh();
    window.codeEditor = editor;
    window.languageModes = languageModes;
    window.defaultCodes = defaultCodes;
});

function runPracticeTests() {
    var scope = Alpine.$data(document.querySelector('[x-data]'));
    if (scope && typeof scope.runTests === 'function') {
        scope.runTests();
    }
}
