import './app.scss';
import 'codemirror/lib/codemirror';
import 'codemirror/mode/php/php';
import 'codemirror/lib/codemirror.css';
import 'codemirror/addon/fold/foldcode';
import 'codemirror/addon/fold/foldgutter';
import 'codemirror/addon/fold/foldgutter.css';
import 'codemirror/addon/fold/comment-fold';
import 'codemirror/addon/fold/brace-fold';
import 'codemirror/addon/edit/matchbrackets';

(function (window, $, CodeMirror) {
    $(document).ready(function () {
        new ComplexityDiff();
    });

    window.ComplexityDiff = function () {
        this.editorLeft = new Editor($('#js-editor-left'));
        this.editorRight = new Editor($('#js-editor-right'));

        $(document).on(
            'click',
            '.js-permalink-create',
            this.handlePermalinkCreate.bind(this)
        );
        $(document).on(
            'click',
            '.js-permalink-copy',
            this.handlePermalinkCopy.bind(this)
        );
    };

    $.extend(ComplexityDiff.prototype, {
        handlePermalinkCreate: function () {
            var self = this;
            $.ajax({
                method: 'POST',
                url: '/permalink',
                data: {
                    left: self.editorLeft.getCode(),
                    right: self.editorRight.getCode(),
                },
            })
                .then(function (data) {
                    $('.js-permalink-input').val(data);
                })
                .catch(function (jqXHR) {
                    console.error(jqXHR);
                });
        },
        handlePermalinkCopy: function () {
            let $input = $('.js-permalink-input');
            $input.select();
            document.execCommand('copy');
        },
    });

    var Editor = function ($wrapper) {
        this.$wrapper = $wrapper;
        this.editor = CodeMirror.fromTextArea($wrapper.find('.editor')[0], {
            mode: 'php',
            lineNumbers: true,
            viewportMargin: Infinity,
            matchBrackets: true,
            indentUnit: 4,
            foldGutter: true,
            gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
        });

        $wrapper.on(
            'click',
            '.js-recalculate-complexities',
            this.handleRecalculate.bind(this)
        );
    };

    $.extend(Editor.prototype, {
        getCode: function () {
            return this.editor.getValue();
        },
        handleRecalculate: function () {
            var self = this;
            $.ajax({
                method: 'POST',
                url: '/calculate',
                data: this.getCode(),
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
})(window, jQuery, CodeMirror);
