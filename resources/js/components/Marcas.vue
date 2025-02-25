<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <card-component
                    titulo="Busca de Marcas">
                    <template v-slot:conteudo>
                        <div class="row">
                            <div class="col mb-3">
                                <input-conteiner-component titulo="ID" id="inputId" id-help="idHelp" texto-ajuda="Opcional. Informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp" placeholder="ID" v-model="busca.id">
                                </input-conteiner-component>
                            </div>

                            <div class="col mb-3">
                                <div class="col mb-3">
                                    <input-conteiner-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp" texto-ajuda="Opcional. Informe o nome da marca">
                                        <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp" placeholder="Nome da Marca" v-model="busca.nome">
                                    </input-conteiner-component>
                                </div>

                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-end" @click="pesquisar">Pesquisar</button>
                    </template>

                </card-component>


                <!-- INICIO LISTAGEM -->
                <card-component titulo="Relação de Marcas">
                    <template v-slot:conteudo>
                        <table-component v-if="marcas.data" :dados="marcas.data" :titulos="{
                                id:{titulo: 'ID', tipo:'text'},
                                nome: {titulo: 'Nome', tipo:'text'},
                                imagem: {titulo: 'Imagem', tipo:'imagem'},
                            created_at: {titulo: 'Data de criação', tipo:'data'},
                            }"
                        :visualizar="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaVisualizar'}"
                        :atualizar="true"
                        :remover="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaRemover'}">
                        </table-component>
                    </template>

                    <template v-slot:rodape>
                        <div class="row">
                            <div class="col-10">

                                <paginate-component>
                                    <li v-for="(l, key) in marcas.links" :key="key" class="page-item" @click="paginacao(l)" :class="l.active ? 'page-item active' : 'page-item'">
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>

                            </div>
                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-end" data-bs-toggle="modal" data-bs-target="#modalMarca">Adicionar</button>
                            </div>
                        </div>
                    </template>
                </card-component>
            </div>
        </div>

        <!-- Modal para cadastrar marca-->
        <modal-component id="modalMarca" titulo="Adicionar Marca">

            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Marca cadastrada com sucesso!" v-if="transacaoStatus === 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao tentar cadastrar a marca" v-if="transacaoStatus === 'erro'"></alert-component>

            </template>


            <template v-slot:conteudo>
                <div class="form-group">
                    <input-conteiner-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp" texto-ajuda="Informe o nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp" placeholder="Nome da Marca" v-model="nomeMarca">
                    </input-conteiner-component>

                </div>

                <div class="form-group">
                    <input-conteiner-component titulo="Imagem" id="novoImagem" id-help="novoImagemHelp" texto-ajuda="Selecione uma imagem (PNG)">
                        <input type="file" class="form-control" id="novoImagem" aria-describedby="novoImagemHelp" placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-conteiner-component>


                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>

        </modal-component>

        <!-- Modal para visualizar marca -->

        <modal-component id="modalMarcaVisualizar" titulo="Visualizar Marca">
            <template v-slot:alertas>

            </template>


            <template v-slot:conteudo>
                <input-conteiner-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-conteiner-component>
                <input-conteiner-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-conteiner-component>
                <input-conteiner-component titulo="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem">
                </input-conteiner-component>
                <input-conteiner-component titulo="Data de criação">
                    <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-conteiner-component>

            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </template>
        </modal-component>



            <!-- Modal de remoção de marca -->
        <modal-component id="modalMarcaRemover" titulo="Remover Marca">
            <template v-slot:alertas>

            </template>

            <template v-slot:conteudo>
                <input-conteiner-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-conteiner-component>
                <input-conteiner-component titulo="Nome da Marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-conteiner-component>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-outline-danger" @click="remover">Remover</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
    </div>
</template>

<script>

export default {
    computed:{
        token(){
            let token = document.cookie.split(';').find(indice => {
                return indice.includes('token=')
            })

            token = token.split('=')[1]

            token = 'Beader ' + token

            return token
        }
    },
    data(){
        return{
            nomeMarca: '',
            arquivoImagem: [],
            urlBase: 'http://127.0.0.1:8000/api/v1/marca',
            urlPaginacao: '',
            urlFiltro: '',
            transacaoStatus: '',
            transacaoDetalhes: {},
            marcas: [],
            busca: {id: '', nome: ''},
        }
    },
    methods: {
        paginacao(l){
            if(l.url) {
                this.urlPaginacao = l.url.split('?')[1]
                this.carregarLista()
            }
        },

        carregarLista() {
            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro;
            axios.get(url)
                .then(response => {
                    this.marcas = response.data
                })
                .catch(errors => {
                    console.log(errors)
                })

            console.log(url)
        },

        carregarImagem(e){
            this.arquivoImagem = e.target.files
        },

        salvar(){
            console.log(this.nomeMarca, this.arquivoImagem)

            let formData = new FormData();
            formData.append('nome', this.nomeMarca)
            formData.append('imagem', this.arquivoImagem[0])

            let config = {
                headers:{
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + response.data.id
                    }
                    console.log(response)
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {
                        mensagem: errors.response.data.message,
                        dados: errors.response.data.errors
                    }

                })
        },

        pesquisar(){
            let filtro = ''
            for(let chave in this.busca){

                if(this.busca[chave]){

                    if (filtro != ''){
                        filtro += ";"
                    }

                    filtro += chave + ':like:' + this.busca[chave]

                }
            }
            if(filtro != '') {
                this.urlPaginacao = 'page=1'
                this.urlFiltro = '&filtro=' + filtro
            } else {
                this.urlFiltro = ''
            }

            this.carregarLista()
        },

        remover()
        {
            let confirmacao = confirm('Tem certeza que deseja remover este registro?')
            if(!confirmacao){
                return false;
            }

            let url = this.urlBase + '/' + this.$store.state.item.id


            axios.post()
                .then(response => {
                    console.log('Registro removido com sucesso!')
                })
                .catch(errors => {
                    console.log('Houve um erro na tentativa de remoção do registro')
                })

        }

    },
    mounted() {
        this.carregarLista()
    }
}
</script>
