Vue.createApp({
    template: `
    <div id="edt">
    <h3>{{ date }}</h3>
        <div class="calendar" >
            <div class="timeline" >
                <div class="time-marker" ></div>
                <div class="time-marker" v-for="heure in heures">{{ heure }}</div>
            </div>
            <div class="days">
                <div class="events">
                    <div class="event securities" v-for="coursCourant in coursSet" :style="[{'grid-row-start': coursCourant.start},{'grid-row-end': coursCourant.end}]">
                        <center>
                            <p class="title" style="text-align: center;" >{{coursCourant.cours.matiere.titre}} {{ coursCourant.cours.salle.numero}}</p>
                            <p style="text-align: center;" >{{coursCourant.cours.professeur.prenom}} {{ coursCourant.cours.professeur.nom}}</p>
                        </center>
                    </div>
                </div>
            </div>
        </div>
        <center>
        <button v-on:click="previousDay()">Jour précédent</button>
        <button v-on:click="nextDay()">Jour suivant</button>
        </center>
    </div>`,
    data() {
        return {
            apiBase: 'http://localhost:8000/api/',
            cours: [],
            coursSet: [],
            heuresBases: ['08H00','09H00','10H00','11H00','12H00','13H00','14H00','15H00','16H00','17H00','18H00'],
            heures: [],
            pagination: 0,
            date: "",
            options: { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }

        }
    },

    methods: {
        getDate: function(){
            var currentDate = new Date();
            currentDate.setDate(currentDate.getDate()+this.pagination);
            var date_string  = currentDate.toLocaleDateString('fr-FR', this.options);
            this.date  = date_string.charAt(0).toUpperCase() + date_string.slice(1);
        },

        nextDay: function(){
            this.pagination+=1;
            this.getCoursByDate();
        },
        previousDay: function(){
            this.pagination-=1;
            this.getCoursByDate();
        },
        getCoursByDate: function(){
            this.getDate();
            var currentDate = new Date();

            currentDate.setDate(currentDate.getDate()+this.pagination);
            var dateString = currentDate.getFullYear()+'-';

            dateString += currentDate.getMonth() < 10 ? '0'+(currentDate.getMonth()+1)+'-' : (currentDate.getMonth()+1)+'-';
            dateString += currentDate.getDate() < 10 ? '0'+currentDate.getDate() : currentDate.getDate();

            axios.post(this.apiBase + 'cours/getByDate',{
                "date": dateString
            })
                .then(response => {
                    this.cours = response.data;
                    this.setHeures();
                })
                .catch(error => {
                    console.log(error)
                })
        },
        setHeures: function() {
            this.heures = this.heuresBases.slice();
            console.log(this.cours);
            this.cours.forEach(element => {
               
                var heureDeb = element.dateHeureDebut.date.split(' ')[1].split(':')[0] + 'H' + element.dateHeureDebut.date.split(' ')[1].split(':')[1];
                var heureFin = element.dateHeureFin.date.split(' ')[1].split(':')[0] + 'H' + element.dateHeureFin.date.split(' ')[1].split(':')[1];
                
                if(this.heures.indexOf(heureDeb) == -1)
                {
                    this.heures.push(heureDeb);
                }

                if(this.heures.indexOf(heureFin) == -1)
                {
                    this.heures.push(heureFin);
                }
                
            });

           this.heures = this.heures.sort();
           this.setJsonCours();
        },
        setJsonCours: function(){
            this.coursSet = [];
            this.cours.forEach(element => {
                var jsonCour = {
                    "cours" : {},
                    "start" : "",
                    "end" : ""
                }
                
                var heureDeb = element.dateHeureDebut.date.split(' ')[1].split(':')[0] + 'H' + element.dateHeureDebut.date.split(' ')[1].split(':')[1];
                var heureFin = element.dateHeureFin.date.split(' ')[1].split(':')[0] + 'H' + element.dateHeureFin.date.split(' ')[1].split(':')[1];
                console.log(this.heures);
                console.log(this.heures.indexOf(heureDeb));
                console.log(this.heures.indexOf(heureFin));
                jsonCour['heureDeb'] = heureDeb;
                jsonCour['heureFin'] = heureFin;
                jsonCour['cours'] = element;
                jsonCour['start'] = this.heures.indexOf(heureDeb)+2;
                jsonCour['end'] = this.heures.indexOf(heureFin)+2;

                console.log(jsonCour);
                this.coursSet.push(jsonCour);
            })
            console.log(this.coursSet);
        }
    },

    mounted() {
        this.getDate();
        this.getCoursByDate()
    }
}).mount('#edt');