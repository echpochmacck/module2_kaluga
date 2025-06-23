$(()=>{

    $('#my-pjax').on('click', 'input[type="radio"]', function (e) {
        $('#product-select_img').val($(this).val());
        console.log($('#product-select_img').val())

    });

    $('#my-pjax').on('change', '#file-input', function (e) {
        var form = new FormData($(this).parents('form').get(0));
        form.append("file", "");
        form.append("files", $(this)[0].files);

    console.log($(this)[0].files)
        var settings = {
            "url": "http://champKaluga/admin/product/check-file",
            "method": "POST",
            "processData": false,
            "mimeType": "multipart/form-data",
            "contentType": false,
            "data": form
        };

        $.ajax(settings).done(function (response) {
           const  data = JSON.parse(response);
            if (data) {
                $('#box .item-img').remove()
                data.forEach((file, index)=>{
                    const  container = `

<div class="item-img p-3 flex-column">
    <img src="/temp/${file}" alt="картинка">
    <div>
        <input type="radio" name="radio" value="${index}">
    </div>
</div>`;
                    $('#box').append(container)

                })
            }
        });
    })
})
