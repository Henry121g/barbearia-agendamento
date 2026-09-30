// Busca os horários livres sempre que o cliente muda serviço, barbeiro ou data.

const campoServico = document.getElementById('servico');
const campoBarbeiro = document.getElementById('barbeiro');
const campoData = document.getElementById('data');
const campoHorario = document.getElementById('horario');

function mostrarOpcao(texto) {
    campoHorario.innerHTML = '';
    campoHorario.add(new Option(texto, ''));
    campoHorario.disabled = true;
}

async function carregarHorarios() {
    if (!campoServico.value || !campoBarbeiro.value || !campoData.value) {
        mostrarOpcao('Escolha serviço, barbeiro e data');
        return;
    }

    mostrarOpcao('Carregando...');

    const parametros = new URLSearchParams({
        servico_id: campoServico.value,
        barbeiro_id: campoBarbeiro.value,
        data: campoData.value,
    });

    try {
        const resposta = await fetch('horarios.php?' + parametros);
        const { horarios } = await resposta.json();

        if (horarios.length === 0) {
            mostrarOpcao('Nenhum horário livre nesse dia');
            return;
        }

        campoHorario.innerHTML = '';
        campoHorario.add(new Option('Selecione', ''));
        horarios.forEach((hora) => campoHorario.add(new Option(hora, hora)));
        campoHorario.disabled = false;
    } catch (erro) {
        mostrarOpcao('Erro ao carregar horários');
    }
}

[campoServico, campoBarbeiro, campoData].forEach((campo) =>
    campo.addEventListener('change', carregarHorarios)
);

carregarHorarios();
