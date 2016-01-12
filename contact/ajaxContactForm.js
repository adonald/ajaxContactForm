/*
 * AJAX contact form for website
 * Version: 0.1.0
 * Author: Alex Donald
 * Website: https://www.adonald.co.uk
 * Date: 2016-01-08
 * License: MIT
 */

var ajaxContactForm = new function () {
    var httpRequest;
    var ajaxContactFormId;
    var ajaxContactFormResultId;
    
    this.setId = function (formId, resultId) {
        ajaxContactFormId = document.getElementById(formId);
        ajaxContactFormResultId = document.getElementById(resultId);
    };
    
    this.initialise = function () {
        ajaxContactFormId.submit.onclick = function () {
            ajaxContactFormId.ajax.value = "true";
            sendForm(ajaxContactFormId);
            return false;
        };
    };
    
    var sendForm = function () {
        httpRequest = new XMLHttpRequest();
        httpRequest.onreadystatechange = displayResult;
        var formData = parseFormData(ajaxContactFormId);
        httpRequest.open('POST', ajaxContactFormId.action);
        httpRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        httpRequest.send(formData);
    };
    
    var displayResult = function () {
        if (httpRequest.readyState === XMLHttpRequest.DONE) {
            if (httpRequest.status === 200) {
                // parse json response
                var result = JSON.parse(httpRequest.response);
                
                // decide if submission was a success or not
                if (result.success) {
                    ajaxContactFormResultId.className = "acf_success";
                    ajaxContactFormId.reset();
                } else {
                    ajaxContactFormResultId.className = "acf_error";
                }
                
                // Display success/error message to user
                ajaxContactFormResultId.innerHTML = result.message;
            } else {
                ajaxContactFormResultId.className = "acf_error";
                ajaxContactFormResultId.innerHTML = "There has been a problem, please try again later.";
            }
        }
    };
    
    var parseFormData = function (form) {
        //This is a bit tricky, [].fn.call(form.elements, ...) allows us to call .fn
        //on the form's elements, even though it's not an array. Effectively
        //Filtering all of the fields on the form
        var params = [].filter.call(form.elements, function(el) {
            return !!el.name; //Nameless elements die.
        })
        .filter(function(el) { return el.disabled === false; }) //Disabled elements die.
        .map(function(el) {
            //Map each field into a name=value string, make sure to properly escape!
            return encodeURIComponent(el.name) + '=' + encodeURIComponent(el.value);
        }).join('&'); //Then join all the strings by &
        return params;
    };
};