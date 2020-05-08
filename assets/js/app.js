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

(function (window, $, monaco) {
    $(document).ready(function () {
        new ComplexityDiff();
    });

    window.ComplexityDiff = function () {
        new Editor($('#js-editor-left'));
        new Editor($('#js-editor-right'));
    };

    var Editor = function ($wrapper) {
        this.$wrapper = $wrapper;
        let $editor = $wrapper.find('.editor');
        let code = $editor.text();
        $editor.html('');
        this.editor = monaco.editor.create($editor[0], {
            value: code,
            minimap: { enabled: false },
            language: 'php',
        });

        $wrapper.on(
            'click',
            '.js-recalculate-complexities',
            this.handleRecalculate.bind(this)
        );
    };

    $.extend(Editor.prototype, {
        handleRecalculate: function () {
            var self = this;
            $.ajax({
                method: 'POST',
                url: '/calculate',
                data: this.editor.getModel().getValue(),
            })
                .then(function (data) {
                    self.$wrapper
                        .find('.js-cyclomatic-complexity')
                        .html(data.cyclomatic_complexity);
                    self.$wrapper
                        .find('.js-cognitive-complexity')
                        .html(data.cognitive_complexity);
                })
                .catch(function (jqXHR) {
                    self.$wrapper.find('.js-cyclomatic-complexity').html('X');
                    self.$wrapper.find('.js-cognitive-complexity').html('X');
                });
        },
    });
})(window, jQuery, monaco);
