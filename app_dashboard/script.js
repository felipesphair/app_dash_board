$(document).ready(() => {
	$('#documentacao').on('click', () => {
        
        //$('#pagina').load('documentacao.html')
        /*$.get('documentacao.html', data => {
            $('#pagina').html(data)
        })*/
        $.post('documentacao.html', data => {
            $('#pagina').html(data)
        })
    })
    $('#suporte').on('click', () => {
        //$('#pagina').load('suporte.html')
        /*$.get('suporte.html', data => {
            $('#pagina').html(data)
        })*/

        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    })

    $('#competencia').on('change', e=> {
        $.ajax({
            type: 'GET'
            ,url: 'app.php',
            data: { competencia: $(e.target).val() },
            dataType: 'json',
            success: dados =>{
                $('#numeroVendas').html(dados.numeroVendas),
                $('#totalVendas').html(dados.totalVendas),
                $('#ativos').html(dados.clientesAtivos),
                $('#inativos').html(dados.clientesInativos),
                $('#elogios').html(dados.TTelogios),
                $('#reclamacoes').html(dados.TTreclamacoes),
                $('#sugestoes').html(dados.TTsugestoes),
                $('#totalDespesas').html(dados.TTdespesas)} ,
            error: erro =>{console.log(erro)}
        })
    })
})