(function ($) {
    document.getElementById('mc-embedded-subscribe').addEventListener('click', function(){
        postData('https://themuseumhouse.com/wp-json/betheme-child/v1/sequent/addprospect', getSubscriptionFormData());
    });

    function getSubscriptionFormData() {
        var formData = {
            "FNAME": document.getElementById('mce-FNAME').value,
            "LNAME": document.getElementById('mce-LNAME').value,
            "EMAIL": document.getElementById('mce-EMAIL').value,
            "MMERGE3": document.getElementById('mce-MMERGE3').value,
            "PHONE": document.getElementById('mce-PHONE').value,
            "MMERGE5": document.getElementById('mce-MMERGE5').value,
            "MMERGE6": document.getElementById('mce-MMERGE6').value,
            "MMERGE8": document.getElementById('mce-MMERGE8').value
        };

        return formData;
    }
    function postData(url, data) {
        console.log('data: ',data);
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            success: function(response) { console.log(response)},
            error: function(error){ console.error(error)}
        });
    }
})(jQuery);

