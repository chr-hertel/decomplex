import 'codemirror/lib/codemirror';
import 'codemirror/mode/php/php';
import 'codemirror/lib/codemirror.css';
import 'codemirror/addon/fold/foldcode';
import 'codemirror/addon/fold/foldgutter';
import 'codemirror/addon/fold/foldgutter.css';
import 'codemirror/addon/fold/comment-fold';
import 'codemirror/addon/fold/brace-fold';
import 'codemirror/addon/edit/matchbrackets';

export let Editor = function ($wrapper) {
    this.$wrapper = $wrapper;

    let editor = $wrapper.find('.editor')[0];
    // workaround to fix reinitialization on history back
    editor.value = $(editor).text();

    this.editor = CodeMirror.fromTextArea(editor, {
        mode: 'php',
        lineNumbers: true,
        viewportMargin: Infinity,
        matchBrackets: true,
        indentUnit: 4,
        foldGutter: true,
        gutters: ['CodeMirror-linenumbers', 'CodeMirror-foldgutter'],
    });

    $wrapper.on('click', '.js-copy-editor', this.copyEditorText.bind(this));
    $wrapper.on(
        'click',
        '.js-calculate-complexities',
        this.handleCalculate.bind(this)
    );
    $wrapper.on(
        'click',
        '.js-simplify-code',
        this.handleSimplifyCode.bind(this)
    );
};

$.extend(Editor.prototype, {
    getCode: function () {
        return this.editor.getValue();
    },
    copyEditorText: function () {
        navigator.clipboard.writeText(this.getCode());
    },
    handleCalculate: function () {
        this.waitingForCalculation();
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
                self.$wrapper.removeClass('calculating');
            })
            .catch(function (jqXHR) {
                let error = { value: 'X', level: 'error' };
                self.setError(jqXHR.responseJSON);
                self.setLevel(self.$wrapper, error.level);
                self.replaceComplexity(
                    self.$wrapper.find('.js-cyclomatic-complexity'),
                    error
                );
                self.replaceComplexity(
                    self.$wrapper.find('.js-cognitive-complexity'),
                    error
                );
                self.$wrapper.removeClass('calculating');
            });
    },
    waitingForCalculation: function () {
        this.$wrapper.addClass('calculating');

        let $cyclo = this.$wrapper.find('.js-cyclomatic-complexity');
        this.replaceComplexity($cyclo, {
            value: '?',
            level: $cyclo.data('complexity-level'),
        });

        let $cogni = this.$wrapper.find('.js-cognitive-complexity');
        this.replaceComplexity($cogni, {
            value: '?',
            level: $cogni.data('complexity-level'),
        });
    },
    replaceComplexity: function ($wrapper, data) {
        this.setLevel($wrapper, data.level);
        $wrapper.find('span').html(data.value);
    },
    setLevel: function ($wrapper, level) {
        let oldLevel = $wrapper.data('complexity-level');
        $wrapper
            .removeClass('complexity-level-' + oldLevel)
            .addClass('complexity-level-' + level)
            .data('complexity-level', level);
    },
    setError: function (errors) {
        let $error = this.$wrapper.find('.code-error span');
        if (undefined === errors || 0 === errors.length) {
            $error.text('Looks like your code has a syntax error!');
        } else {
            $error.text(errors[0].message + ' (Line ' + errors[0].line + ')');
        }
    },
    handleSimplifyCode: function () {
        this.waitingForCalculation();
        var self = this;
        $.ajax({
            method: 'POST',
            url: '/simplify',
            data: this.getCode(),
        })
            .then(function (data) {
                self.setLevel(self.$wrapper, data.complexity_level);
                self.replaceCode(data.code);
                self.replaceComplexity(
                    self.$wrapper.find('.js-cyclomatic-complexity'),
                    data.cyclomatic_complexity
                );
                self.replaceComplexity(
                    self.$wrapper.find('.js-cognitive-complexity'),
                    data.cognitive_complexity
                );
                self.$wrapper.removeClass('calculating');
            })
            .catch(function (jqXHR) {
                let error = { value: 'X', level: 'error' };
                self.$wrapper
                    .find('.code-error span')
                    .text('Unable to simplify your code.');
                self.setLevel(self.$wrapper, error.level);
                self.replaceComplexity(
                    self.$wrapper.find('.js-cyclomatic-complexity'),
                    error
                );
                self.replaceComplexity(
                    self.$wrapper.find('.js-cognitive-complexity'),
                    error
                );
                self.$wrapper.removeClass('calculating');
            });
    },
    replaceCode: function (code) {
        this.editor.setValue(code);
    },
});
