<template>
  <section class="section">
    <div class="container">
      <h1 class="title">
        JackHammer
      </h1>
      <div>
        <p>
          Jackhammer is a game played on a triangular mesh, similar to that used in Chinese checkers.
          There are three players, each start in one corner of the mesh. The size of the mesh has a maximum of 17 units
          / positions on a side.
        </p>
        <br /><br />
        <div v-if="!isHidden">
          <form>
            <div class="form-row">
              <div class="col-6">
                <label for="formControlRange">How many units? : {{form.unitinput}}</label>
                <input type="range" class="form-control-range" min="4" max="17"  name="units" v-model.number="form.unitinput">
              </div>
              <div class="col-6">
              </div>
              <div class="col-6">
              </div>
              <div class="col-6">
                <label for="inputCity">How many turns?</label>
                <input type="text" class="form-control" id="turns" name="turns" v-model="form.turns">
              </div>
              <br />
            </div>
          </form>
          <button class="btn btn-success btn-lg btn-block" v-on:click="startGame()" :disabled='isDisabled'>Start Game</button>

        </div>

      </div>
    </div>

    <div>
      <center>
        <div class="container">
          <section id="game">
            <div class="gameboard">
              <div v-for="(units, index) in form.units" class="board2" v-bind:id="units">
                  <div v-for="(unit, index2) in units" v-bind:id="unit" class="Dot" v-on:click="divclick(units,unit)">
                    <div class='dotBorder' v-if="units == 1" v-bind:id="units+'.'+unit"><div class='blueDot'></div></div>
                    <div class='dotBorder' v-if="units == form.units && index2 == 0" v-bind:id="units+'.'+unit"><div class='redDot'></div></div>
                    <div class='dotBorder' v-if="units == form.units && unit == units" v-bind:id="units+'.'+unit"><div class='greenDot'></div></div>
                    <div class='dotBorder' v-if="units != form.units && units != 1" v-bind:id="units+'.'+unit"><div class='whiteDot'></div></div>
                    <div class='dotBorder' v-if="units != 1 && (units == form.units && index2 != 0) && (units == form.units && unit != units)" v-bind:id="units+'.'+unit"><div class='whiteDot'></div></div>
                    <!--<div id="1"><div class='closes' ></div></div>-->
                  </div>
                  <br />
              </div>
            </div>
          </section>
          <br />
          <div v-if="isHidden">
              <p v-if="myKey == 'blue'"><a class="btn btn-primary btn-lg" href="#" role="button" disabled>YOU ARE BLUE</a></p>
              <p v-if="myKey == 'red'"><a class="btn btn-danger btn-lg" href="#" role="button" disabled>YOU ARE RED</a></p>
              <p v-if="myKey == 'green'"><a class="btn btn-success btn-lg" href="#" role="button" disabled>YOU ARE GREEN</a></p>
          </div>
        </div>
      </center>
    </div>
  </section>
</template>

<script>
    import io from "socket.io-client";
    export default {
      // set initial vars
        data() {
            return {
                socket: {},
                context: {},
                form: {
                  unitinput: 4,
                  units: 0,
                  turns: 0
                },
                myKey: '',
                componentKey: 0,
                isHidden: false,
                isDisabled: true,
                turn: '',
                color: {
                  red: false,
                  green: false,
                  blue: false
                },
                positions: [],
                current: {
                  blue: 0,
                  red: 0,
                  green: 0
                },
                isvalid: false
            }
        },

        //create socket connection
        created() {
          this.socket = io("http://localhost:3000");
        },
        beforeMount(){
          this.read();
        },
        mounted() {
          this.socket.on("clients", data => {
            if (data >= 3){
              this.isDisabled = false;
            }
          });

          this.socket.on("isHidden", data => {
            this.form.units = Number(data);
            this.isHidden = true;

            //setting initial positions
            this.current.blue = 1.1;
            this.current.red = this.form.units + '.' + 1;
            this.current.green = this.form.units + '.' + this.form.units;

            this.myKey = sessionStorage.getItem('myKey');
          });

          this.socket.on("positions", data => {
            this.positions = data;
          });

          this.socket.on("incomplete", data => {
            // alert when a player quits
            alert(data);
            this.isHidden = false;
            this.isDisabled = true;
            this.form.units = 0;
            this.update(1, false);
            this.update(2, false);
            this.update(3, false);
          });
        },
        methods: {
            forceRerender() {
              this.componentKey += 1;
            },
            startGame() {
              // initiate start match with default var values
              this.form.units = this.form.unitinput;

              this.socket.emit("hidden", this.form.units);
            },
            divclick(unit,index){
              console.log(this.myKey+' clicked circle: '+unit+' , '+index);

              this.validatemove(unit+"."+index);

              if (this.isvalid != false)
              {
                if(this.myKey == 'blue'){
                  console.log('current position: '+this.current.blue);
                  document.getElementById(this.current.blue).innerHTML = "<div class='whiteDot'></div>";
                  document.getElementById(unit+"."+index).innerHTML = "<div class='blueDot'></div>";
                  this.current.blue = unit+"."+index;
                }
                if(this.myKey == 'red'){
                  console.log('current position: '+this.current.red);
                  document.getElementById(this.current.red).innerHTML = "<div class='whiteDot'></div>";
                  document.getElementById(unit+"."+index).innerHTML = "<div class='redDot'></div>";
                  this.current.red = unit+"."+index;
                }
                if(this.myKey == 'green'){
                  console.log('current position: '+this.current.green);
                  document.getElementById(this.current.green).innerHTML = "<div class='whiteDot'></div>";
                  document.getElementById(unit+"."+index).innerHTML = "<div class='greenDot'></div>";
                  this.current.green = unit+"."+index;
                }

                this.positions.push({"key":this.myKey, "move":unit+"."+index});
              } else {
                console.log('invalid move.');
                //this.positions.push({"key":this.myKey, "x":0, "y":0});
              }

              console.log(JSON.stringify(this.positions));

              // @todo emit position: check if everyone has completed a move for the current the turn
              this.socket.emit("position", this.positions);
            },
            checkkey(){
              console.log('myKey: ' + sessionStorage.getItem('myKey'));
            },
            read() {
              window.axios.get('/api/colors').then(({ data }) => {
                for (let d in data) {
                  console.log(data[d].color + ' : ' + data[d].taken);
                  if(data[d].color == 'red' && data[d].taken == true){this.color.red = true;}
                  if(data[d].color == 'blue' && data[d].taken == true){this.color.blue = true;}
                  if(data[d].color == 'green' && data[d].taken == true){this.color.green = true;}
                }

                //lets set a color for each connected client
                if(!this.color.red){
                  console.log('I am red');
                  sessionStorage.setItem('myKey', 'red');
                  this.color.red = true;
                  this.socket.emit("color", 'red');
                  this.update(1, true);
                }
                else if(!this.color.blue){
                  console.log('I am blue');
                  sessionStorage.setItem('myKey', 'blue');
                  this.color.blue = true;
                  this.socket.emit("color", 'blue');
                  this.update(2, true);
                }
                else if(!this.color.green){
                  console.log('I am green');
                  sessionStorage.setItem('myKey', 'green');
                  this.color.green = true;
                  this.socket.emit("color", 'green');
                  this.update(3, true);
                }
                else{
                  alert('Please Restart Node Socket!')
                }

              });
            },
            update(id, taken) {
              window.axios.patch(`/api/colors/${id}`, { taken }).then(() => {
                // Once AJAX resolves we can update the Crud with the new color
                //this.cruds.find(crud => crud.id === id).color = color;
              }).catch(error => {
                console.log(error.message);
              })
            },
            validatemove(id) {
              // @todo during turn reset turn session to false
              // reset all positions
              if (sessionStorage.getItem('turn') == 'true'){
                console.log('already set move.');
                this.isvalid = false;
              }
              else{
                sessionStorage.setItem('turn',true);
                this.isvalid = true;
              }

            }
        }
    }
</script>

<style scoped></style>
