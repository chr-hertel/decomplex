import '../scss/app.scss';

import 'monaco-editor/esm/vs/editor/browser/controller/coreCommands.js';
import 'monaco-editor/esm/vs/editor/contrib/find/findController.js';
import * as monaco from 'monaco-editor/esm/vs/editor/editor.api.js';
import 'monaco-editor/esm/vs/basic-languages/php/php.contribution.js';

self.MonacoEnvironment = {
    getWorkerUrl: function (moduleId, label) {
        return './editor.worker.bundle.js';
    },
};

monaco.editor.create(document.getElementById('js-editor-left'), {
    value: ['<?php', ''].join('\n'),
    minimap: { enabled: false },
    language: 'php',
});

monaco.editor.create(document.getElementById('js-editor-right'), {
    value: ['<?php', ''].join('\n'),
    minimap: { enabled: false },
    language: 'php',
});
