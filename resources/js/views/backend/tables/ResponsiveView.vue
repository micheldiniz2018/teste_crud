<script setup>
import {reactive, onUnmounted, onMounted} from "vue";
import {
    Dataset,
    DatasetItem,
    DatasetInfo,
    DatasetPager,
    DatasetSearch,
    DatasetShow,
} from "vue-dataset";

// Colunas Tabelas Cargos
const cargo_colunas = reactive([
    {
        name: "Cargo",
        field: "cargo",
        sort: "",
    },
    {
        name: "Ações",
        field: "acoes",
        sort: "",
    },
]);

// Colunas Tabelas Cargos
const user_colunas = reactive([
    {
        name: "Nome",
        field: "nome",
        sort: "desc",
    },
    {
        name: "Cargo",
        field: "cargo",
        sort: "",
    },
    {
        name: "Departamento",
        field: "departamento",
        sort: "",
    },
    {
        name: "Centro de Custo",
        field: "centro_custo",
        sort: "",
    },
    {
        name: "CPF",
        field: "cpf",
        sort: "",
    },
    {
        name: "Ações",
        field: "acoes",
        sort: "",
    },
]);

// Colunas Tabela Centro de Custo
const centro_custo_colunas = reactive([
    {
        name: "Centro de Custo",
        field: "centro_custo",
        sort: "",
    },
    {
        name: "Ações",
        field: "acoes",
        sort: "",
    },
]);

// Colunas Tabela Departamento
const departamento_colunas = reactive([
    {
        name: "Departamento",
        field: "departamento",
        sort: "",
    },
    {
        name: "Centro de Custo",
        field: "centro_custo",
        sort: "",
    },
    {
        name: "Ações",
        field: "acoes",
        sort: "",
    },
]);

let popoverTriggerList = [];
let popoverList = [];

// Apply a few Bootstrap 5 optimizations
onMounted(() => {
    // Remove labels from
    document.querySelectorAll("#datasetLength label").forEach((el) => {
        el.remove();
    });

    // Replace select classes
    let selectLength = document.querySelector("#datasetLength select");

    selectLength.classList = "";
    selectLength.classList.add("form-select");
    selectLength.style.width = "80px";
    popoverTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="popover"]')
    );

    // ..and init them
    popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new window.bootstrap.Popover(popoverTriggerEl, {
            container: popoverTriggerEl.dataset.bsContainer || "#page-container",
            animation:
                popoverTriggerEl.dataset.bsAnimation &&
                popoverTriggerEl.dataset.bsAnimation.toLowerCase() == "true"
                    ? true
                    : false,
            trigger: popoverTriggerEl.dataset.bsTrigger || "hover focus",
        });
    });
});

// Dispose popovers on unMounted
onUnmounted(() => {
    popoverList.forEach((popover) => popover.dispose());
});
</script>

<script>
import axios from 'axios'
import {mask} from 'vue-the-mask'

const form_cargo = reactive({
    cargo: '',
    id: ''
});

const form_centro_custo = reactive({
    centro_custo: '',
    id: ''
});

const form_departamento = reactive({
    departamento: '',
    id: '',
    id_centro_custo: ''
});

const form_centro_custo_filtro = reactive({
    id_centro_custo: '',
});

const form_departamento_filtro = reactive({
    id: '',
});

function Crud({descricao, id}) {
    this.cargo = descricao;
    this.id = id;
};

const form_user = reactive({
    id: '',
    nome: '',
    sobre_nome: '',
    idade: '',
    cpf: '',
    email: '',
    departamento: '',
    departamento_id: '',
    cargo_id: ''
});

function CrudUser({
                      id,
                      name,
                      idade,
                      cpf,
                      email,
                      departamento,
                      cargo,
                      centro_custo,
                      cargo_id,
                      departamento_id,
                      sobrenome
                  }) {
    this.id = id;
    this.nome = name;
    this.idade = idade;
    this.cpf = cpf;
    this.email = email;
    this.departamento = departamento;
    this.cargo = cargo;
    this.centro_custo = centro_custo;
    this.cargo_id = cargo_id;
    this.departamento_id = departamento_id;
    this.sobre_nome = sobrenome;
};

function CrudCentroCusto({descricao, id}) {
    this.centro_custo = descricao;
    this.id = id;
};

function CrudDepartamento({descricao, id, centro_custo, id_centro_custo}) {
    this.departamento = descricao;
    this.id = id;
    this.centro_custo = centro_custo;
    this.id_centro_custo = id_centro_custo;
};

function CrudDepartamentoFiltro({descricao, id}) {
    this.departamento = descricao;
    this.id = id;
};

const alertas = reactive({
    titulo: '',
    corpo: '',
    class_titulo: '',
    class_corpo: ''
});

const atualizar_atributo = reactive({
    titulo: '',
    id: '',
    class_titulo: '',
    class_corpo: '',
    valor: '',
    id_centro_custo: ''
});

export default {
    data() {
        this.read();
        this.get_centro_custo();
        this.get_departamentos();
        return {
            cargo_array: [],
            centro_custo_array: [],
            departamento_array: [],
            user_array: [],
            user_update_array: [],
            departamento_filtro_array: [],
            url_update: '',
            mute: false
        }
    },
    directives: {mask},
    methods: {
        async importar_usuarios() {
            var arquivo = document.getElementById("arquivo_importar").files[0];
            let data = new FormData();

            if(!arquivo){
                alertas.titulo = 'Atenção!';
                alertas.corpo = 'Nenhum Arquivo Selecionado';
                alertas.class_titulo = 'text-danger';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();
                return;
            }

            data.append('file_csv', arquivo, arquivo.name);
            data.append('title', this.title);

            try {
                const resposta = await axios.post('api/user/importar', data);
                var retorno = JSON.parse(resposta.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();
            } catch (e) {
                this.returnError(e);
            }
        },
        async limpar_dados_usuario() {
            form_user.nome = '';
            form_user.sobre_nome = '';
            form_user.idade = '';
            form_user.cpf = '';
            form_user.email = '';
            form_user.departamento = '';
            form_user.departamento_id = '';
            form_user.cargo_id = '';
            form_user.cargo_id = '';
        },
        async update_user() {
            this.mute = true;
            this.url_update = '/api/user/atualizar';

            try {
                const resp = await axios.post(this.url_update, form_user);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.get_user();

            } catch (e) {
                var errors = JSON.parse(e.request.response).error;

                for (var field in errors) {
                    alertas.titulo = 'Atenção!';
                    alertas.corpo = errors[field];
                    alertas.class_titulo = 'text-danger';
                    alertas.class_corpo = 'text-light';

                    var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                        keyboard: false
                    });
                    modal.show();
                    return;
                }
            }

            this.mute = false;
        },
        async update_user_modal(id) {
            form_user.id = id;
            const retono_array = [];
            const {data} = await axios.post('/api/user/atualizarModal', form_user);
            data.forEach(crud => retono_array.push(new CrudUser(crud)));

            form_user.id = retono_array[0].id;
            form_user.nome = retono_array[0].nome;
            form_user.sobre_nome = retono_array[0].sobre_nome;
            form_user.cpf = retono_array[0].cpf;
            form_user.email = retono_array[0].email;
            form_user.idade = retono_array[0].idade;
            form_user.departamento = retono_array[0].departamento;
            form_user.departamento_id = retono_array[0].departamento_id;
            form_user.cargo_id = retono_array[0].cargo_id;
            form_user.cargo = retono_array[0].cargo;

            var modal_atualizar_atributo = new bootstrap.Modal(document.getElementById('modal-alterar-usuario'), {
                keyboard: false
            });

            modal_atualizar_atributo.show();
        },
        async deletar_user(id, valor) {
            if (!window.confirm('Tem certeza que deseja excluir o usuário ' + valor + '?')) {
                return
            }

            this.mute = true;
            form_user.id = id;

            try {
                const resp = await axios.post('/api/user/deletar', form_user);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.user_array = [];
                this.get_user();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async get_user() {
            this.user_array = [];
            const {data} = await axios.post('/api/user/listar', form_departamento_filtro);
            data.forEach(crud => this.user_array.push(new CrudUser(crud)));
        },
        async create_user() {
            this.mute = true;
            this.url_update = '/api/user/criar';

            try {
                const resp = await axios.post(this.url_update, form_user);
                var retorno = JSON.parse(resp.request.response);

                form_user.nome = '';
                form_user.sobre_nome = '';
                form_user.idade = '';
                form_user.cpf = '';
                form_user.email = '';
                form_user.departamento = '';
                form_user.departamento_id = '';
                form_user.cargo_id = '';

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

            } catch (e) {
                var errors = JSON.parse(e.request.response).error;

                for (var field in errors) {
                    alertas.titulo = 'Atenção!';
                    alertas.corpo = errors[field];
                    alertas.class_titulo = 'text-danger';
                    alertas.class_corpo = 'text-light';

                    var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                        keyboard: false
                    });
                    modal.show();
                    return;
                }
            }

            this.mute = false;
        },
        async filtro_departamento(id) {
            if ((id)) {
                this.departamento_filtro_array = [];
                form_centro_custo_filtro.id_centro_custo = id.target.selectedOptions[0].value;

                const {data} = await axios.post('/api/departamento/filtro', form_centro_custo_filtro);
                data.forEach(crud => this.departamento_filtro_array.push(new CrudDepartamentoFiltro(crud)));
            } else {
                this.departamento_filtro_array = [];
            }
        },
        async update_departamento() {
            var modal_departamento = new bootstrap.Modal(document.getElementById('atualizar_departamento'), {});

            modal_departamento._hideModal();

            //remove classe de bloqueio do background de qualquer modal
            var class_hide = document.getElementsByClassName('modal-backdrop');
            while (class_hide.length > 0) {
                class_hide[0].parentNode.removeChild(class_hide[0]);
            }

            try {
                const resp = await axios.post(this.url_update, atualizar_atributo);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.departamento_array = [];
                this.get_departamentos();
                this.url_update = '';
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async update_departamento_modal(id, valor, id_centro_custo) {
            atualizar_atributo.titulo = 'Atualizar Departamento';
            atualizar_atributo.class_titulo = 'text-info';
            atualizar_atributo.class_corpo = 'text-light';
            atualizar_atributo.id_centro_custo = id_centro_custo;
            atualizar_atributo.valor = valor;
            atualizar_atributo.id = id;

            var modal_atualizar_atributo = new bootstrap.Modal(document.getElementById('atualizar_departamento'), {
                keyboard: false
            });

            modal_atualizar_atributo.show();
            this.url_update = '/api/departamento/atualizar';
        },
        async delete_departamento(id, valor) {
            if (!window.confirm('Tem certeza que deseja excluir o departamento ' + valor + '?')) {
                return
            }

            this.mute = true;
            form_departamento.id = id;

            try {
                const resp = await axios.post('/api/departamento/deletar', form_departamento);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.departamento_array = [];
                this.get_departamentos();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async get_departamentos() {
            const {data} = await axios.get('/api/departamento/listar');
            data.forEach(crud => this.departamento_array.push(new CrudDepartamento(crud)));
        },
        async create_departamento() {
            this.mute = true;

            try {
                const resp = await axios.post('/api/departamento/criar', form_departamento);
                form_departamento.departamento = '';
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.departamento_array = [];
                this.centro_custo_array = [];
                this.get_departamentos();
                this.get_centro_custo();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async update_centro_custo_modal(id, valor) {
            atualizar_atributo.titulo = 'Atualizar Centro de Custo';
            atualizar_atributo.class_titulo = 'text-info';
            atualizar_atributo.class_corpo = 'text-light';
            atualizar_atributo.valor = valor;
            atualizar_atributo.id = id;

            var modal_atualizar_atributo = new bootstrap.Modal(document.getElementById('atualizar_atributo_pessoa'), {
                keyboard: false
            });

            modal_atualizar_atributo.show();
            this.url_update = '/api/centro_custo/atualizar';
        },
        async delete_centro_custo(id, valor) {
            if (!window.confirm('Tem certeza que deseja excluir o centro de custo ' + valor + '?')) {
                return
            }

            this.mute = true;
            form_centro_custo.id = id;

            try {
                const resp = await axios.post('/api/centro_custo/deletar', form_centro_custo);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.centro_custo_array = [];
                this.get_centro_custo();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async get_centro_custo() {
            const {data} = await axios.get('/api/centro_custo/listar');
            data.forEach(crud => this.centro_custo_array.push(new CrudCentroCusto(crud)));
        },
        async create_centro_custo() {
            this.mute = true;

            try {
                const resp = await axios.post('/api/centro_custo/criar', form_centro_custo);
                form_centro_custo.centro_custo = '';
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.centro_custo_array = [];
                this.get_centro_custo();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async returnError(e) {
            if (e.response.status === 422) {
                var retorno = JSON.parse(e.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-danger';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();
            } else {
                alertas.titulo = 'Atenção!';
                alertas.corpo = e.message;
                alertas.class_titulo = 'text-danger';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();
            }
        },
        async create_cargo() {
            this.mute = true;

            try {
                const resp = await axios.post('/api/cargo/criar', form_cargo);
                form_cargo.cargo = '';
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.cargo_array = [];
                this.read();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async read() {
            const {data} = await axios.get('/api/cargo/listar');
            data.forEach(crud => this.cargo_array.push(new Crud(crud)));
        },
        async updateModal(id, valor) {
            atualizar_atributo.titulo = 'Atualizar Cargo';
            atualizar_atributo.class_titulo = 'text-info';
            atualizar_atributo.class_corpo = 'text-light';
            atualizar_atributo.valor = valor;
            atualizar_atributo.id = id;

            var modal_atualizar_atributo = new bootstrap.Modal(document.getElementById('atualizar_atributo_pessoa'), {
                keyboard: false
            });

            modal_atualizar_atributo.show();

            this.url_update = '/api/cargo/atualizar';
        },
        async update() {
            var modal_atualizar_atributo = new bootstrap.Modal(document.getElementById('atualizar_atributo_pessoa'), {});

            modal_atualizar_atributo._hideModal();

            //remove classe de bloqueio do background de qualquer modal
            var class_hide = document.getElementsByClassName('modal-backdrop');
            while (class_hide.length > 0) {
                class_hide[0].parentNode.removeChild(class_hide[0]);
            }

            try {
                const resp = await axios.post(this.url_update, atualizar_atributo);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.centro_custo_array = [];
                this.cargo_array = [];
                this.read();
                this.get_centro_custo();
                this.url_update = '';
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        },
        async del(id, cargo) {
            if (!window.confirm('Tem certeza que deseja excluir o cargo ' + cargo + '?')) {
                return
            }

            this.mute = true;
            form_cargo.id = id;

            try {
                const resp = await axios.post('/api/cargo/deletar', form_cargo);
                var retorno = JSON.parse(resp.request.response);

                alertas.titulo = 'Atenção!';
                alertas.corpo = retorno.message;
                alertas.class_titulo = 'text-success';
                alertas.class_corpo = 'text-light';

                var modal = new bootstrap.Modal(document.getElementById('modal_alerta'), {
                    keyboard: false
                });
                modal.show();

                this.cargo_array = [];
                this.read();
            } catch (e) {
                this.returnError(e);
            }

            this.mute = false;
        }
    }
};
</script>

<style lang="scss" scoped>
.gg-select {
    box-sizing: border-box;
    position: relative;
    display: block;
    transform: scale(1);
    width: 22px;
    height: 22px;
}

.gg-select::after,
.gg-select::before {
    content: "";
    display: block;
    box-sizing: border-box;
    position: absolute;
    width: 8px;
    height: 8px;
    left: 7px;
    transform: rotate(-45deg);
}

.gg-select::before {
    border-left: 2px solid;
    border-bottom: 2px solid;
    bottom: 4px;
    opacity: 0.3;
}

.gg-select::after {
    border-right: 2px solid;
    border-top: 2px solid;
    top: 4px;
    opacity: 0.3;
}

th.sort {
    cursor: pointer;
    user-select: none;

    &.asc {
        .gg-select::after {
            opacity: 1;
        }
    }

    &.desc {
        .gg-select::before {
            opacity: 1;
        }
    }
}
</style>

<template>
    <!-- Hero -->
    <BasePageHeading
        title="Gerenciamento de Usuário"
    >
        <template #extra>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-alt">
                    <li class="breadcrumb-item">
                        <a class="link-fx" href="javascript:void(0)">Gerenciamento</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Usuário</li>
                </ol>
            </nav>
        </template>
    </BasePageHeading>
    <!-- END Hero -->

    <!-- Page Content -->
    <div class="content">
        <!-- buttons gerenciar -->
        <BaseBlock title="Opções">
            <div class="row push">
                <div class="col-sm-2 col-xl-2 m-2">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            @click="limpar_dados_usuario" data-bs-target="#modal-incluir-usuario">
                        Incluir Usuário
                    </button>
                </div>
                <div class="col-sm-2 col-xl-2 m-2">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#modal-gerenciar-cargo">Gerenciar Cargo
                    </button>
                </div>
                <div class="col-sm-2 col-xl-3 m-2">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#modal-gerenciar-centro-custo">Gerenciar Centro de Custo
                    </button>
                </div>
                <div class="col-sm-2 col-xl-3 m-2">
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#modal-gerenciar-departamento">Gerenciar Departamento
                    </button>
                </div>
                <div class="col-sm-2 col-xl-3 m-2">
                    <div class="mb-4">
                        <label class="form-label" for="arquivo_importar"
                        >Importar por Arquivo CSV</label>
                        <input class="form-control" type="file" id="arquivo_importar"/>
                    </div>
                </div>
                <div class="col-sm-2 col-xl-3 m-2" style="margin-top: 2.5% !important;">
                    <button class="btn btn-success float-right"
                            @click="importar_usuarios">Importar
                    </button>
                </div>
            </div>
        </BaseBlock>
        <!-- end buttons gerenciar -->
        <!-- form -->
        <BaseBlock title="Filtro de Usuários">
            <form @submit.prevent>
                <div class="row push">
                    <div class="col-lg-12 col-xl-3">
                        <div class="mb-3">
                            <label class="form-label" for="centro_custo">Centro de Custo</label>
                            <select
                                class="form-select"
                                id="centro_custo"
                                name="centro_custo"
                                v-model="form_centro_custo_filtro.id_centro_custo"
                                @change="filtro_departamento"
                            >
                                <option value="">Selecione um Centro de Custo</option>
                                <option
                                    v-for="col in centro_custo_array"
                                    :value="col.id"
                                >{{ col.centro_custo }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-3">
                        <div class="mb-3">
                            <label class="form-label" for="departamentos_filtro">
                                Departamentos
                                <i class="fas fa-info-circle"
                                   data-bs-toggle="popover"
                                   data-bs-placement="top"
                                   title="Atenção"
                                   data-bs-content="Para listar os departamentos, por favor selecione um centro de custo."></i>
                            </label>
                            <select
                                class="form-select"
                                id="departamentos_filtro"
                                name="departamentos_filtro"
                                v-model="form_departamento_filtro.id"
                            >
                                <option value="">Selecione um Departamento</option>
                                <option
                                    v-for="col in departamento_filtro_array"
                                    :value="col.id"
                                >{{ col.departamento }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="row push">
                        <div class="col-lg-12 col-xl-3">
                            <div class="mb-4">
                                <button type="submit" @click="get_user" class="btn btn-primary">Filtrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </BaseBlock>
        <!-- end form -->

        <!-- Table -->
        <BaseBlock title="Tabela de Usuários">
            <Dataset
                v-slot="{ ds }"
                :ds-data="user_array"
                :ds-search-in="['cargo']"
            >
                <div class="row" :data-page-count="ds.dsPagecount">
                    <div id="datasetLength" class="col-md-8 py-2">
                        <DatasetShow :ds-show-entries="5"/>
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0">
                                <thead>
                                <tr>
                                    <th
                                        v-for="(th, index) in user_colunas"
                                        :key="th.field"
                                        @click="onSort($event, index)"
                                    >
                                        {{ th.name }} <i class="gg-select float-end"></i>
                                    </th>
                                </tr>
                                </thead>
                                <DatasetItem tag="tbody" class="fs-sm">
                                    <template #default="{ row, rowIndex }">
                                        <tr>
                                            <td style="width: 20%">{{ row.nome }}</td>
                                            <td style="width: 15%">{{ row.cargo }}</td>
                                            <td style="width: 15%">{{ row.departamento }}</td>
                                            <td style="width: 15%">{{ row.centro_custo }}</td>
                                            <td style="width: 10%">{{ row.cpf }}</td>
                                            <td style="width: 5%" class="text-center">
                                                <div class="btn-group">
                                                    <button type="button"
                                                            class="btn btn-sm btn-alt-secondary"
                                                            @click="update_user_modal(row.id)">
                                                        <i class="fa fa-fw fa-pencil-alt"></i>
                                                    </button>
                                                    <button type="button"
                                                            @click="deletar_user(row.id,row.nome)"
                                                            class="btn btn-sm btn-alt-secondary">
                                                        <i class="fa fa-fw fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </DatasetItem>
                            </table>
                        </div>
                    </div>
                </div>
                <div
                    class="d-flex flex-md-row flex-column justify-content-between align-items-center"
                >
                    <DatasetInfo class="py-3 fs-sm"/>
                    <DatasetPager class="flex-wrap py-3 fs-sm"/>
                </div>
            </Dataset>
        </BaseBlock>
        <!-- Table -->
    </div>


    <!-- Modal Alterar Usuário -->
    <div
        class="modal"
        id="modal-alterar-usuario"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-alterar-usuario"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <BaseBlock title="Alterar de Usuário" transparent class="mb-0">
                    <template #options>
                        <button
                            type="button"
                            class="btn-block-option"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </template>

                    <template #content>
                        <div class="block-content fs-sm">
                            <form @submit.prevent>
                                <div class="row push">
                                    <div class="col-lg-12">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label" for="user_nome_form">Nome</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="user_nome_form"
                                                    name="user_nome_form"
                                                    placeholder="Nome"
                                                    v-model="form_user.nome"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="user_sobrenome_form">Sobrenome</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="user_sobrenome_form"
                                                    name="user_sobrenome_form"
                                                    placeholder="Sobrenome"
                                                    v-model="form_user.sobre_nome"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="user_idade_form">Idade</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="user_idade_form"
                                                    name="user_idade_form"
                                                    placeholder="Idade"
                                                    v-model="form_user.idade"
                                                    v-mask="['###']"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row push">
                                    <div class="col-lg-12">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label" for="user_cpf_form">CPF</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="user_cpf_form"
                                                    name="user_cpf_form"
                                                    placeholder="CPF"
                                                    v-model="form_user.cpf"
                                                    v-mask="['###.###.###-##']"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="user_email_form">E-mail</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="user_email_form"
                                                    name="user_email_form"
                                                    placeholder="E-mail"
                                                    v-model="form_user.email"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="departamentos_filtro">
                                                    Departamento
                                                </label>
                                                <select
                                                    class="form-select"
                                                    id="departamentos_filtro"
                                                    name="departamentos_filtro"
                                                    v-model="form_user.departamento_id"
                                                >
                                                    <option value="">Selecione um Departamento</option>
                                                    <option
                                                        v-for="col in departamento_array"
                                                        :value="col.id"
                                                    >{{ col.departamento }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="cargo_filtro">
                                                    Cargo
                                                </label>
                                                <select
                                                    class="form-select"
                                                    id="cargo_filtro_filter"
                                                    name="cargo_filtro_filter"
                                                    v-model="form_user.cargo_id"
                                                >
                                                    <option value="">Selecione um Cargo</option>
                                                    <option
                                                        v-for="col in cargo_array"
                                                        :value="col.id"
                                                    >{{ col.cargo }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="block-content block-content-full text-end bg-body">
                            <button
                                type="button"
                                class="btn btn-sm btn-primary"
                                id="incluir_usuario_button"
                                @click="update_user"
                            >
                                Gravar
                            </button>
                        </div>
                    </template>
                </BaseBlock>
            </div>
        </div>
    </div>
    <!-- END Modal Alterar Usuário -->

    <!-- Modal Incluir Usuário -->
    <div
        class="modal"
        id="modal-incluir-usuario"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-incluir-usuario"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <BaseBlock title="Incluir de Usuário" transparent class="mb-0">
                    <template #options>
                        <button
                            type="button"
                            class="btn-block-option"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </template>

                    <template #content>
                        <div class="block-content fs-sm">
                            <form @submit.prevent>
                                <div class="row push">
                                    <div class="col-lg-12">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label" for="user_nome_form">Nome</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="user_nome_form"
                                                    name="user_nome_form"
                                                    placeholder="Nome"
                                                    v-model="form_user.nome"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="user_sobrenome_form">Sobrenome</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="user_sobrenome_form"
                                                    name="user_sobrenome_form"
                                                    placeholder="Sobrenome"
                                                    v-model="form_user.sobre_nome"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="user_idade_form">Idade</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="user_idade_form"
                                                    name="user_idade_form"
                                                    placeholder="Idade"
                                                    v-model="form_user.idade"
                                                    v-mask="['###']"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row push">
                                    <div class="col-lg-12">
                                        <div class="row g-2">
                                            <div class="col-4">
                                                <label class="form-label" for="user_cpf_form">CPF</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="user_cpf_form"
                                                    name="user_cpf_form"
                                                    placeholder="CPF"
                                                    v-model="form_user.cpf"
                                                    v-mask="['###.###.###-##']"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="user_email_form">E-mail</label>
                                                <input
                                                    type="email"
                                                    class="form-control"
                                                    id="user_email_form"
                                                    name="user_email_form"
                                                    placeholder="E-mail"
                                                    v-model="form_user.email"
                                                />
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="departamentos_filtro">
                                                    Departamento
                                                </label>
                                                <select
                                                    class="form-select"
                                                    id="departamentos_filtro"
                                                    name="departamentos_filtro"
                                                    v-model="form_user.departamento_id"
                                                >
                                                    <option value="">Selecione um Departamento</option>
                                                    <option
                                                        v-for="col in departamento_array"
                                                        :value="col.id"
                                                    >{{ col.departamento }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <label class="form-label" for="cargo_filtro">
                                                    Cargo
                                                </label>
                                                <select
                                                    class="form-select"
                                                    id="cargo_filtro_filter"
                                                    name="cargo_filtro_filter"
                                                    v-model="form_user.cargo_id"
                                                >
                                                    <option value="">Selecione um Cargo</option>
                                                    <option
                                                        v-for="col in cargo_array"
                                                        :value="col.id"
                                                    >{{ col.cargo }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="block-content block-content-full text-end bg-body">
                            <button
                                type="button"
                                class="btn btn-sm btn-primary"
                                id="incluir_usuario_button"
                                @click="create_user"
                            >
                                Gravar
                            </button>
                        </div>
                    </template>
                </BaseBlock>
            </div>
        </div>
    </div>
    <!-- END Modal Incluir Usuário -->

    <!-- Modal Gerenciar Cargo -->
    <div
        class="modal"
        id="modal-gerenciar-cargo"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-gerenciar-cargo"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <BaseBlock title="Gerenciar Cargo" transparent class="mb-0">
                    <template #options>
                        <button
                            type="button"
                            class="btn-block-option"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </template>
                    <template #content>
                        <div class="block-content fs-sm">
                            <!-- Inline -->
                            <BaseBlock title="Incluir Novo Cargo" content-full>
                                <div class="row">
                                    <div class="col-lg-8 space-y-2">
                                        <!-- Form Inline - Default Style -->
                                        <div
                                            class="row row-cols-lg-auto g-3 align-items-center"
                                        >
                                            <div class="col-12">
                                                <label class="visually-hidden" for="cargo_incluir"
                                                >Cargo</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="cargo_incluir"
                                                    name="cargo_incluir"
                                                    placeholder="Cargo"
                                                    v-model="form_cargo.cargo"
                                                />
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary" @click="create_cargo">
                                                    Incluir
                                                </button>
                                            </div>
                                        </div>
                                        <!-- END Form Inline - Default Style -->
                                    </div>
                                </div>
                            </BaseBlock>
                            <!-- END Inline -->
                            <!-- Table -->
                            <BaseBlock title="Tabela de Cargos">
                                <Dataset
                                    v-slot="{ ds }"
                                    :ds-data="cargo_array"
                                    :ds-search-in="['cargo']"
                                >
                                    <div class="row" :data-page-count="ds.dsPagecount">
                                        <div id="datasetLength" class="col-md-8 py-2">
                                            <DatasetShow :ds-show-entries="5"/>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th
                                                            v-for="(th, index) in cargo_colunas"
                                                            :key="th.field"
                                                            @click="onSort($event, index)"
                                                        >
                                                            {{ th.name }} <i class="gg-select float-end"></i>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <DatasetItem tag="tbody" class="fs-sm">
                                                        <template #default="{ row, rowIndex }">
                                                            <tr>
                                                                <td style="width: 80%">{{ row.cargo }}</td>
                                                                <td style="width: 15%" class="text-center">
                                                                    <div class="btn-group">
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-alt-secondary"
                                                                                @click="updateModal(row.id,row.cargo)">
                                                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                                @click="del(row.id,row.cargo)"
                                                                                class="btn btn-sm btn-alt-secondary">
                                                                            <i class="fa fa-fw fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </DatasetItem>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex flex-md-row flex-column justify-content-between align-items-center"
                                    >
                                        <DatasetInfo class="py-3 fs-sm"/>
                                        <DatasetPager class="flex-wrap py-3 fs-sm"/>
                                    </div>
                                </Dataset>
                            </BaseBlock>
                            <!-- Table -->
                        </div>
                    </template>
                </BaseBlock>
            </div>
        </div>
    </div>
    <!-- END Modal Gerenciar Cargo -->

    <!-- Modal Gerenciar Departamento -->
    <div
        class="modal"
        id="modal-gerenciar-departamento"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-gerenciar-departamento"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <BaseBlock title="Gerenciar Departamentos" transparent class="mb-0">
                    <template #options>
                        <button
                            type="button"
                            class="btn-block-option"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </template>
                    <template #content>
                        <div class="block-content fs-sm">
                            <!-- Inline -->
                            <BaseBlock title="Incluir Departamentos" content-full>
                                <div class="row">
                                    <div class="col-lg-8 space-y-2">
                                        <!-- Form Inline - Default Style -->
                                        <div
                                            class="row row-cols-lg-auto g-3 align-items-center"
                                        >
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label" for="centro_custo_departamento">Centro de
                                                        Custo</label>
                                                    <select
                                                        class="form-select"
                                                        id="centro_custo_departamento"
                                                        name="centro_custo_departamento"
                                                        v-model="form_departamento.id_centro_custo"
                                                    >
                                                        <option
                                                            v-for="col in centro_custo_array"
                                                            :value="col.id"
                                                        >{{ col.centro_custo }}
                                                        </option>
                                                    </select>
                                                </div>
                                                <label class="form-label"
                                                       for="departamento_incluir">Departamento</label>
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="departamento_incluir"
                                                    name="departamento_incluir"
                                                    placeholder="Departamento"
                                                    v-model="form_departamento.departamento"
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary"
                                                    @click="create_departamento">
                                                Incluir
                                            </button>
                                        </div>
                                        <!-- END Form Inline - Default Style -->
                                    </div>
                                </div>
                            </BaseBlock>
                            <!-- END Inline -->
                            <!-- Table -->
                            <BaseBlock title="Tabela de Departamentos">
                                <Dataset
                                    v-slot="{ ds }"
                                    :ds-data="departamento_array"
                                    :ds-search-in="['departamento']"
                                >
                                    <div class="row" :data-page-count="ds.dsPagecount">
                                        <div id="datasetLength" class="col-md-2 py-2">
                                            <DatasetShow :ds-show-entries="5"/>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th
                                                            v-for="(th, index) in departamento_colunas"
                                                            :key="th.field"
                                                            @click="onSort($event, index)"
                                                        >
                                                            {{ th.name }} <i class="gg-select float-end"></i>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <DatasetItem tag="tbody" class="fs-sm">
                                                        <template #default="{ row, rowIndex }">
                                                            <tr>
                                                                <td style="width: 40%">{{ row.departamento }}</td>
                                                                <td style="width: 40%">{{ row.centro_custo }}</td>
                                                                <td style="width: 15%" class="text-center">
                                                                    <div class="btn-group">
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-alt-secondary"
                                                                                @click="update_departamento_modal(row.id,row.departamento,row.id_centro_custo)">
                                                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                                @click="delete_departamento(row.id,row.departamento)"
                                                                                class="btn btn-sm btn-alt-secondary">
                                                                            <i class="fa fa-fw fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </DatasetItem>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex flex-md-row flex-column justify-content-between align-items-center"
                                    >
                                        <DatasetInfo class="py-3 fs-sm"/>
                                        <DatasetPager class="flex-wrap py-3 fs-sm"/>
                                    </div>
                                </Dataset>
                            </BaseBlock>
                            <!-- Table -->
                        </div>
                    </template>
                </BaseBlock>
            </div>
        </div>
    </div>
    <!-- END Modal Gerenciar Departamento -->

    <!-- Modal Gerenciar Centro Custo -->
    <div
        class="modal"
        id="modal-gerenciar-centro-custo"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modal-gerenciar-centro-custo"
        aria-hidden="true"
    >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <BaseBlock title="Gerenciar Centro de Custo" transparent class="mb-0">
                    <template #options>
                        <button
                            type="button"
                            class="btn-block-option"
                            data-bs-dismiss="modal"
                            aria-label="Close"
                        >
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                    </template>
                    <template #content>
                        <div class="block-content fs-sm">
                            <!-- Inline -->
                            <BaseBlock title="Incluir Centro de Custo" content-full>
                                <div class="row">
                                    <div class="col-lg-8 space-y-2">
                                        <!-- Form Inline - Default Style -->
                                        <div
                                            class="row row-cols-lg-auto g-3 align-items-center"
                                        >
                                            <div class="col-12">
                                                <label class="visually-hidden" for="example-if-email"
                                                >Email</label
                                                >
                                                <input
                                                    type="text"
                                                    class="form-control"
                                                    id="centro_custo_incluir"
                                                    name="centro_custo_incluir"
                                                    placeholder="Centro de Custo"
                                                    v-model="form_centro_custo.centro_custo"
                                                />
                                            </div>
                                            <div>
                                                <button type="submit" class="btn btn-primary"
                                                        @click="create_centro_custo">
                                                    Incluir
                                                </button>
                                            </div>
                                        </div>
                                        <!-- END Form Inline - Default Style -->
                                    </div>
                                </div>
                            </BaseBlock>
                            <!-- END Inline -->
                            <!-- Table -->
                            <BaseBlock title="Tabela de Centro de Custo">
                                <Dataset
                                    v-slot="{ ds }"
                                    :ds-data="centro_custo_array"
                                    :ds-search-in="['centro_custo']"
                                >
                                    <div class="row" :data-page-count="ds.dsPagecount">
                                        <div id="datasetLength" class="col-md-2 py-2">
                                            <DatasetShow :ds-show-entries="5"/>
                                        </div>
                                    </div>
                                    <hr/>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="table table-striped mb-0">
                                                    <thead>
                                                    <tr>
                                                        <th
                                                            v-for="(th, index) in centro_custo_colunas"
                                                            :key="th.field"
                                                            @click="onSort($event, index)"
                                                        >
                                                            {{ th.name }} <i class="gg-select float-end"></i>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <DatasetItem tag="tbody" class="fs-sm">
                                                        <template #default="{ row, rowIndex }">
                                                            <tr>
                                                                <td style="width: 80%">{{ row.centro_custo }}</td>
                                                                <td style="width: 15%" class="text-center">
                                                                    <div class="btn-group">
                                                                        <button type="button"
                                                                                class="btn btn-sm btn-alt-secondary"
                                                                                @click="update_centro_custo_modal(row.id,row.centro_custo)">
                                                                            <i class="fa fa-fw fa-pencil-alt"></i>
                                                                        </button>
                                                                        <button type="button"
                                                                                @click="delete_centro_custo(row.id,row.centro_custo)"
                                                                                class="btn btn-sm btn-alt-secondary">
                                                                            <i class="fa fa-fw fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </template>
                                                    </DatasetItem>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex flex-md-row flex-column justify-content-between align-items-center"
                                    >
                                        <DatasetInfo class="py-3 fs-sm"/>
                                        <DatasetPager class="flex-wrap py-3 fs-sm"/>
                                    </div>
                                </Dataset>
                            </BaseBlock>
                            <!-- Table -->
                        </div>
                    </template>
                </BaseBlock>
            </div>
        </div>
    </div>
    <!-- END Modal Gerenciar Departamento -->

    <!-- Modal Alerta -->
    <div class="modal fade" id="modal_alerta" tabindex="-1" aria-labelledby="modal_alerta_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title alerta-titulo" :class="alertas.class_titulo" id="modal_alerta_label">
                        {{ alertas.titulo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body alerta-corpo" :class="alertas.class_corpo">
                    {{ alertas.corpo }}
                </div>
                <div class="modal-footer">
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Alert -->

    <!-- Modal AtualizarElemento -->
    <div class="modal fade" id="atualizar_atributo_pessoa" tabindex="-1"
         aria-labelledby="modal_atualizar_atributo_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title alerta-titulo" :class="atualizar_atributo.class_titulo"
                        id="modal_atualizar_atributo_label">{{ atualizar_atributo.titulo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body alerta-corpo" :class="atualizar_atributo.class_corpo">
                    <BaseBlock title="" content-full>
                        <div class="row">
                            <div class="col-lg-8 space-y-2">
                                <!-- Form Inline - Default Style -->
                                <div
                                    class="row row-cols-lg-auto g-3 align-items-center"
                                >
                                    <div class="col-12">
                                        <label class="visually-hidden" for="example-if-email"
                                        >Email</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="atualizar_cargo"
                                            name="atualizar_cargo"
                                            placeholder="Cargo"
                                            v-model="atualizar_atributo.valor"
                                            onfocus="this"
                                        />
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary" @click="update">
                                            Atualizar
                                        </button>
                                    </div>
                                </div>
                                <!-- END Form Inline - Default Style -->
                            </div>
                        </div>
                    </BaseBlock>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Atualziar Elemento -->


    <!-- Modal Atualizar Departamento -->
    <div class="modal fade" id="atualizar_departamento" tabindex="-1"
         aria-labelledby="modal_departamento_label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title alerta-titulo" :class="atualizar_atributo.class_titulo"
                        id="modal_departamento_label">{{ atualizar_atributo.titulo }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body alerta-corpo" :class="atualizar_atributo.class_corpo">
                    <BaseBlock title="" content-full>
                        <div class="row">
                            <div class="col-lg-8 space-y-2">
                                <!-- Form Inline - Default Style -->
                                <div
                                    class="row row-cols-lg-auto g-3 align-items-center"
                                >
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="centro_custo_departamento">Centro de
                                                Custo</label>
                                            <select
                                                class="form-select"
                                                id="centro_custo_departamento"
                                                name="centro_custo_departamento"
                                                v-model="atualizar_atributo.id_centro_custo"
                                            >
                                                <option
                                                    v-for="col in centro_custo_array"
                                                    :value="col.id"
                                                >{{ col.centro_custo }}
                                                </option>
                                            </select>
                                        </div>
                                        <label class="form-label" for="atualizar_cargo">Departamento</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="atualizar_cargo"
                                            name="atualizar_cargo"
                                            placeholder="Cargo"
                                            v-model="atualizar_atributo.valor"
                                            onfocus="this"
                                        />
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary" @click="update_departamento">
                                            Atualizar
                                        </button>
                                    </div>
                                </div>
                                <!-- END Form Inline - Default Style -->
                            </div>
                        </div>
                    </BaseBlock>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal Atualziar Departamento -->

    <!-- END Page Content -->
</template>
