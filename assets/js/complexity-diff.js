import { Editor } from './editor';

export let ComplexityDiff = function () {
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
        if (!navigator.clipboard){
            document.execCommand('copy');
        } else{
            navigator.clipboard.writeText($input.val());
        }
    },
});
