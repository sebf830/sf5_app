/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import 'jquery/dist/jquery.min.js'


$('.valid_message').on('click', function (e) {
    e.preventDefault()

    var data = {
        'title': $('#message_title').val(),
        'content': $('#message_content').val(),
        'id': $('#numero').val(),
    }

    if ($('#message_title').val() != "" && $('#message_content').val() != "") {
        $.ajax({
            method: "post",
            url: '/send_message',
            headers: {
                'Content-Type': 'application/json'
            },
            dataType: "json",
            data: JSON.stringify(data),
            success: function (response) {
                console.log(response)
            }
        })
        window.location.href = ""
        alert('salut')
    } else {
        $('.error_modal_login').html('Veuillez remplir tous les champs');
    }
})
