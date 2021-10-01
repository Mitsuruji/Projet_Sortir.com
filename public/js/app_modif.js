
    var $ville = $('#sortie_form_sortieVille');
    // When first field gets selected ...
    $ville.change(function() {
        // ... retrieve the corresponding form.
        var $form = $(this).closest('form');
        // Simulate form data, but only include the selected  value.
        var data = {};
        data[$ville.attr('name')] = $ville.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url : $form.attr('action'),
            type: $form.attr('method'),
            data : data,
            method: 'POST',
            success: function(html) {
                // Replace current position field ...
                $('#sortie_form_sortieLieu').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#sortie_form_sortieLieu')
                );
                // Position field now displays the appropriate positions.
            }
        });
    });