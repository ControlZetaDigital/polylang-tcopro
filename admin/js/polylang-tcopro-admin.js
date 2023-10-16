/* Vanilla JS & jQuery Admin Scripts */

(function ($) {
    'use strict';

    const prefix = plytco.pluginName + '-';

    $(document).ready(function() {
        plytcoEventListeners();
    });

    const plytcoEventListeners = () => {
        $('.'+prefix+'flag').on('click', function(e) {
            e.preventDefault();

            var $assignment = $(this).closest('.'+prefix+'assignment');
            var values = ($assignment.find('input').val() !== '') ? $assignment.find('input').val().split('|') : [];

            $(this).toggleClass('selected');

            if ($(this).hasClass('selected')) {
                values.push($(this).data('language'));
            } else {
                values = arrayRemove(values, $(this).data('language'));
            }

            $assignment.find('input').val(values.join('|'));
        });
    }

    const arrayRemove = (arr, value) => {
        return arr.filter(function (el) {
            return el != value;
        });
    }

})(jQuery)