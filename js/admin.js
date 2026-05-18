$(document).ready(function() {
    if ($('#adminTable').length) {
        $('#adminTable').DataTable({
            "language": {
                "search": "Filter list:"
            },
            "paging": false, 
            "scrollY": "400px", 
            "scrollCollapse": true, 
            "info": false 
        });
    }
});

var modal = document.getElementById("mySimpleModal");
var messageText = document.getElementById("modalMessage");
var proceedButton = document.getElementById("modalProceed");
var formToSubmit = null; 

function showModal(message, targetLink, e) {
    var evt = e || window.event;
    if (evt) evt.preventDefault();
    
    messageText.innerText = message;
    proceedButton.href = targetLink;
    proceedButton.onclick = null; 
    
    modal.style.display = "flex"; 
}

function showFormModal(message, buttonElement, e) {
    var evt = e || window.event;
    if (evt) evt.preventDefault();
    
    messageText.innerText = message;
    proceedButton.href = "#"; 
    
    formToSubmit = buttonElement.closest('form'); 
    
    proceedButton.onclick = function(clickEvent) {
        clickEvent.preventDefault();
        
        if (buttonElement.name) {
            var hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = buttonElement.name;
            hiddenInput.value = buttonElement.value;
            formToSubmit.appendChild(hiddenInput);
        }
        
        formToSubmit.submit();
    };
    
    modal.style.display = "flex"; 
}

function closeModal() {
    modal.style.display = "none";
}