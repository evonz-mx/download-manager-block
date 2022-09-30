$( document ).ready(function() {
    $('div[data-dlm-block-data]').each(function() {

        var data = $(this).data('dlm-block-data');
        var select = $(this).find('select.download-os');
        var checkboxes = $(this).find('input[type=checkbox]');
        var download_button = $(this).find('button');


        console.log(data);

    });
});