const baseUrl = document.querySelector('#base_url').value;
const comprarForm = document.querySelector('#comprar_form');
const pacoteSelect = document.querySelector('#pacote_id');
const usernameInput = document.querySelector('#username');
const emailInput = document.querySelector('#email');
const valorInput = document.querySelector('#valor');
const creditosInput = document.querySelector('#creditos');
const paypalForm = document.querySelector('#paypal_form');

buscarPacote();

pacoteSelect.addEventListener('change', () => {
    buscarPacote();
});

comprarForm.addEventListener('submit', (e) => {

    e.preventDefault();

    const form = new FormData();
    form.append('pacote_id', pacoteSelect.value);
    form.append('username', usernameInput.value);
    form.append('email', emailInput.value);

    fetch(baseUrl+"/processos/comprar.php", {
        method: 'POST',
        body: form
    }).then((response) => {
        return response.json();
    }).then((response) => {
        console.log(response);
        if(response.success){
            window.location.href = response.dados.link;
        }else{
            alert(response.msg);
        }
    }).catch((e) => {
        alert("Erro ao registrar compra Paypal Order: "+e.message);
    });
});