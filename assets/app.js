import './app.scss';
import { ComplexityDiff } from './js/complexity-diff';

(function ($, ComplexityDiff) {
    $(document).ready(function () {
        new ComplexityDiff();
    });
})(jQuery, ComplexityDiff);
