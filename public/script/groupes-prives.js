var div = document.getElementById('selectBox');


div.addEventListener("change", () => {

        console.log(document.getElementById('participant'));
        document.getElementById('selectBox').appendChild(document.getElementById('participant'))


})