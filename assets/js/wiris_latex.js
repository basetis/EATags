
$("#update_on_evernote").on('click',
    function () {
        var data = editor.getMathML();
        data = data.replace(" xmlns=\"http://www.w3.org/1998/Math/MathML\"", "");

        var jqxhr = $.post(send_to_evernote_url, {mathML : data, formula : formula_id},
            function(data) {
                $("#server_msg_div").html(data);
                if (data.indexOf("Error:") == -1) {
                    $("#server_msg_div").toggleClass("alert alert-success");
                } else {
                    $("#server_msg_div").toggleClass("alert alert-error");
                }
        })
            .done(function(data) {
                console.log('done');
                console.log(data);
            })
            .fail(function(data) {
                console.error('error');
                console.error(data);
            }
        );
    }
);

var editor;
var last_formula = "";
window.onload = function () {
    editor = com.wiris.jsEditor.JsEditor.newInstance({'language': 'en'});
    editor.insertInto(document.getElementById('editorContainer'));

    editor.setMathML(initial_formula);

    window.setInterval(function(){
        var current_formula = editor.getMathML();
        if (last_formula != current_formula) {
            last_formula = current_formula;
            getLaTeX(current_formula,
                function(response) {
                    $("#latex_text").html(latex_editor_latex_text_div_title + response);
                }
            );
        }

    },2000);
};

function getLaTeX(mathml, callback) {
    var req = new XMLHttpRequest();
    req.open("POST","https://www.wiris.net/demo/editor/mathml2latex", true);
    req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    var params = "mml="+encodeURIComponent(mathml);

    req.onreadystatechange = function () {
        if (req.readyState == 4) {
            if (req.status != 200)  {
                console.log("Error generating LaTeX.");
            }
            else {
                callback(req.responseText);
            }
        }
    };

    req.send(params);
}
