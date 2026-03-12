function buscarPacote(){
    fetch(baseUrl+"/processos/get-pacote.php?pacote_id="+pacoteSelect.value)
    .then((response) => {
        return response.json();
    }).then((response) => {
        console.log(response);
        if(response.success){
            const valorInput = document.querySelector('#valor');
            const creditosInput = document.querySelector('#creditos');
            valorInput.value = response.dados.valor_formatado;
            creditosInput.value = response.dados.creditos_formatado;
        }else{
            console.log(response.msg);
        }
    }).catch((e) => {
        console.log("Erro ao buscar dados do pacote: "+e.message);
    });
}