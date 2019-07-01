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
              <br /><br />
              <div class="btn-group" role="group" id="toolBtns">
                <p>
                  <b>Choose your move</b>
                </p>
                <button class="btn" type="button" @click="activeBtn = 'btn1'" :class="{active: activeBtn === 'btn1' }">Hammer</button>
                <button class="btn" type="button" @click="activeBtn = 'btn2'" :class="{active: activeBtn === 'btn2' }">Move Cell</button>
                <button class="btn" type="button" @click="activeBtn = 'btn3'" :class="{active: activeBtn === 'btn3' }">Skip Turn</button>
              </div>
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
                isvalid: false,
                activeBtn:''
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
            var blue = this.current.blue;
            var red = this.current.red;
            var green = this.current.green;

            console.log(red + " " + blue + " " + green);
            if (this.positions.length == 3) {
              this.positions.forEach(function(position){

                console.log('position key: ' + position.key);

                if(position.key == 'blue'){
                  if(position.move != 0) {
                    document.getElementById(blue).innerHTML = "<div class='whiteDot'></div>";
                    document.getElementById(position.move).innerHTML = "<div class='blueDot'></div>";
                    blue = position.move;
                  }
                }
                if(position.key == 'red'){
                  if(position.move != 0) {
                    document.getElementById(red).innerHTML = "<div class='whiteDot'></div>";
                    document.getElementById(position.move).innerHTML = "<div class='redDot'></div>";
                    red = position.move;
                  }
                }
                if(position.key == 'green'){
                  if(position.move != 0) {
                    document.getElementById(green).innerHTML = "<div class='whiteDot'></div>";
                    document.getElementById(position.move).innerHTML = "<div class='greenDot'></div>";
                    green = position.move;
                  }
                }

              });
              // @todo emit to resolve all moves and empty positions object
              this.current.red = red;
              this.current.blue = blue;
              this.current.green = green;

              this.positions = [];

              sessionStorage.setItem('turn',false);
            }
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
              if(this.activeBtn == '') {
                alert('please choose a move type');
              }
              else {
                console.log(this.myKey+' clicked circle: '+ unit +' , '+ index);

                this.validatemove(unit+"."+index);

                console.log(JSON.stringify(this.positions));

                // @todo emit position: check if everyone has completed a move for the current the turn
                this.socket.emit("position", this.positions);
              }

            },
            checkkey(){
              console.log('myKey: ' + sessionStorage.getItem('myKey'));
            },
            read() {
              window.axios.get('/api/colors').then(({ data }) => {
                for (let d in data) {
                  if(data[d].color == 'red' && data[d].taken == true){this.color.red = true;}
                  if(data[d].color == 'blue' && data[d].taken == true){this.color.blue = true;}
                  if(data[d].color == 'green' && data[d].taken == true){this.color.green = true;}
                }

                //lets set a color for each connected client
                if(!this.color.red){
                  sessionStorage.setItem('myKey', 'red');
                  this.color.red = true;
                  this.socket.emit("color", 'red');
                  this.update(1, true);
                }
                else if(!this.color.blue){
                  sessionStorage.setItem('myKey', 'blue');
                  this.color.blue = true;
                  this.socket.emit("color", 'blue');
                  this.update(2, true);
                }
                else if(!this.color.green){
                  sessionStorage.setItem('myKey', 'green');
                  this.color.green = true;
                  this.socket.emit("color", 'green');
                  this.update(3, true);
                }
                else{
                  alert('Please exit and restart Node Socket!')
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
              this.isvalid = false;

              var tmp = 0;
              // reset all positions
              if (sessionStorage.getItem('turn') == 'true')
              {
                alert('already set move.');
              }
              else
              {
                sessionStorage.setItem('turn',true);

                if(this.myKey == 'blue'){
                  tmp = parseFloat(this.current.blue);
                }
                if(this.myKey == 'red'){
                  tmp = parseFloat(this.current.red);
                }
                if(this.myKey == 'green'){
                  tmp = parseFloat(this.current.green);
                }

                switch(id) {
                  case ( tmp - 1.1 ).toFixed(1):
                  console.log(tmp + '-1.1 ' + (tmp - 1.1));
                    this.isvalid = true;
                    break;
                  case (tmp - 1.0 ).toFixed(1):
                  console.log(tmp + '-1.0' + (tmp - 1.0));
                    this.isvalid = true;
                    break;
                  case ( tmp + 1.1 ).toFixed(1):
                  console.log(tmp + '+1.1' + (tmp + 1.1));
                    this.isvalid = true;
                    break;
                  case ( tmp + 1.0 ).toFixed(1):
                  console.log(tmp + '+1.0' + (tmp + 1.0));
                    this.isvalid = true;
                    break;
                  case ( tmp + 0.1 ).toFixed(1):
                  console.log(tmp + '+0.1' + (tmp + 0.1));
                    this.isvalid = true;
                    break;
                  case ( tmp - 0.1 ).toFixed(1):
                  console.log(tmp + '-0.1' + (tmp - 0.1));
                    this.isvalid = true;
                    break;
                  case tmp:
                  console.log('myself: ' + tmp);
                    this.isvalid = true;
                    break;
                  default:
                    this.isvalid = false;
                }

                // resolve moves
                if (this.isvalid != false)
                {
                  this.positions.push({"key":this.myKey, "move":id});
                } else {
                  alert("Invalid move, you've lost a turn");
                  this.positions.push({"key":this.myKey, "move":0});
                }

              } // else

            }// validate move function
        }
    }
</script>

<style scoped></style>
