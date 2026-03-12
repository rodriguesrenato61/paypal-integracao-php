const baseUrl = document.querySelector('#base_url').value;
const comprarForm = document.querySelector('#comprar_form');
const pacoteSelect = document.querySelector('#pacote_id');
const usernameInput = document.querySelector('#username');
const emailInput = document.querySelector('#email');
const valorInput = document.querySelector('#valor');
const creditosInput = document.querySelector('#creditos');
const paypalForm = document.querySelector('#paypal_form');

buscarPacote();

function buscarPacote(){
    fetch(baseUrl+"/processos/get-pacote.php?pacote_id="+pacoteSelect.value)
    .then((response) => {
        return response.json();
    }).then((response) => {
        console.log(response);
        if(response.success){
            valorInput.value = response.dados.valor_formatado;
            creditosInput.value = response.dados.creditos_formatado;
        }else{
            console.log(response.msg);
        }
    }).catch((e) => {
        console.log("Erro ao buscar dados do pacote: "+e.message);
    });
}

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
            const dados = response.dados;
            paypalForm.action = dados.action;
            paypalForm.cmd.value = dados.cmd;
            paypalForm.amount.value = dados.amount;
            paypalForm.business.value = dados.business;
            paypalForm.item_name.value = dados.item_name;
            paypalForm.currency_code.value = dados.currency_code;
            paypalForm.no_note.value = dados.no_note;
            paypalForm.no_shipping.value = dados.no_shipping;
            paypalForm.rm.value = dados.rm;
            paypalForm.custom.value = dados.custom;
            paypalForm.return.value = dados.return;
            paypalForm.cancel_return.value = dados.cancel_return;
            paypalForm.notify_url.value = dados.notify_url;
            
            paypalForm.submit();
        }else{
            alert(response.msg);
        }
    }).catch((e) => {
        alert("Erro ao registrar compra Paypal IPN: "+e.message);
    });
});