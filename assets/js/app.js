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
            scrollBeyondLastLine: false,
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
                    self.setLevel(self.$wrapper, data.complexity_level);
                    self.replaceComplexity(
                        self.$wrapper.find('.js-cyclomatic-complexity'),
                        data.cyclomatic_complexity
                    );
                    self.replaceComplexity(
                        self.$wrapper.find('.js-cognitive-complexity'),
                        data.cognitive_complexity
                    );
                })
                .catch(function (jqXHR) {
                    let error = { value: 'X', level: 'very-high' };
                    self.setLevel(self.$wrapper, error.level);
                    self.replaceComplexity(
                        self.$wrapper.find('.js-cyclomatic-complexity'),
                        error
                    );
                    self.replaceComplexity(
                        self.$wrapper.find('.js-cognitive-complexity'),
                        error
                    );
                });
        },
        replaceComplexity: function ($wrapper, data) {
            this.setLevel($wrapper, data.level);
            $wrapper.html(data.value);
        },
        setLevel: function ($wrapper, level) {
            let oldLevel = $wrapper.data('complexity-level');
            $wrapper
                .removeClass('complexity-level-' + oldLevel)
                .addClass('complexity-level-' + level)
                .data('complexity-level', level);
        },
    });
})(window, jQuery, monaco);
