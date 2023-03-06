
const { createApp } = Vue

createApp({
    template: 
`<div id="edt" class="container">
    <div class="row">
        <div class="col-8">
            <h1>Note ton prof</h1>
            <div class="row">
                <!-- un professeur -->
                <div class="col-6" v-for="professeur in professeurs">
                    <div class="card">
                        <div class="card-body" :class="{'bg-light' : professeur.id === professeurCourant?.id}">
                            <div class="card-title"></div>
                                {{ professeur.prenom }} {{ professeur.nom }}
                            <div class="card-text">
                                {{ professeur.email }} <br/>
                                <span v-for="matiere in professeur.matieres">
                                    {{ matiere.titre }} {{ matiere.reference }}
                                </span>

                                <button v-on:click="getAvis(professeur)" class="btn btn-primary mt-3">
                                    Avis
                                </button> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4" v-if="professeurCourant">
            <h2>Avis sur {{ professeurCourant.prenom }} {{ professeurCourant.nom }}</h2>
            <form class="mb-5" v-on:submit.prevent="addAvis()">
                <div class="form-group">
                    <label>Note</label>
                    <select class="form-control" v-model="nouvelAvis.note">
                        <option>0</option>
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                </div>

                <div class="form-group" >
                    <label>Commentaire</label>
                    <textarea class="form-control" v-model="nouvelAvis.commentaire" required></textarea>
                </div>

                <div class="form-group" >
                    <label>Email</label>
                    <input type="email" class=" form-control"  v-model="nouvelAvis.emailEtudiant" required>
                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Ajouter">
                </div>
            </form>
            <ul class="text-danger">
                <li v-for="error in errors">{{ error }}</li>
            </ul>

            <div class="card mt-1" v-for="unAvis in avis">
                <div class="card-body">
                    <h5 class="card-title">
                        Note: {{ unAvis.note }} / 5
                    </h5>
                    <p class="card-text">
                        <i>Commentaire de {{ unAvis.emailEtudiant }}</i><br/>
                        {{ unAvis.commentaire }}
                    </p>
                    <button v-on:click="deleteAvis(unAvis)" class="btn btn-primary mt-3">
                        Supprimer
                    </button> 
                </div>
                
            </div>
        </div>
    </div>
</div>`,
    data() {
    return {
        apiBase : "http://localhost:8000/api/",
        professeurs: [],
        avis: [],
        professeurCourant: null,
        nouvelAvis: {},
        errors: []
    }
    },
    methods: {
        deleteAvis: async function(avis){
            try {
                const response = await axios.delete(this.apiBase + `avis/${avis.id}`);
                console.log(response.data); 
                this.avis.splice(this.avis.indexOf(avis),1);
            } catch (error) {
                console.error(error);
            }
        },

        addAvis: async function(){
            try {
                const response = await axios.post(this.apiBase + `professeurs/${this.professeurCourant.id}/avis`, this.nouvelAvis);
                console.log(response.data); 
                this.avis.push(response.data);
                this.errors = [];
            } catch (error) {
                console.error(error);
                this.errors = Object.values(error.response.data);
            }
        },

        createAvis: function(){
            return{
                note: 0 ,
                commentaire: '',
                emailEtudiant: ''
            }
        },
        getProfesseurs: async function(){
            try {
                const response = await axios.get(this.apiBase + 'professeurs');
                console.log(response.data); 
                this.professeurs = response.data;
            } catch (error) {
                console.error(error);
            }
        },

        getAvis: async function(professeur){
            this.nouvelAvis = this.createAvis();
            this.professeurCourant = professeur;
            try {
                const response = await axios.get(this.apiBase + `professeurs/${professeur.id}/avis`);
                console.log(response.data); 
                this.avis = response.data;
            } catch (error) {
                console.error(error);
            }
        }
    },
    mounted(){
        this.getProfesseurs();
    }
}).mount('#edt')
