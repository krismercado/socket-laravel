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
                <input type="number" class="form-control" id="turns" name="turns" min="1" v-model="form.turns">
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
                <button class="btn" type="button" @click="divclick(0,0)">Skip Turn</button>
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
                  turns: 1
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
                activeBtn:'',
                move_type: ''
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

          this.socket.on("turns", data => {
            this.form.turns = Number(data);
          });

          this.socket.on("positions", data => {
            // @todo increase turn count here
            // if reached max turn, calculate total score
            // sort this list by points, if points is equal, sort by name.
            this.positions = helper.arr.multisort(data, ['move'], ['ASC']);

            var blue = this.current.blue;
            var red = this.current.red;
            var green = this.current.green;

            if ((this.positions.length == 3) && (this.form.turns != 0)) {
              // resolves the moves first before hammer if the same cell
              var dups = [];
              var arr = this.positions.filter(function(el) {
                if (dups.indexOf(el.move) == -1) {
                  dups.push(el.move);
                  return true;
                }
                return false;
              });

              this.positions = arr;

              console.log(this.positions);

              this.positions.forEach(function(position){
                if(position.key == 'blue'){
                  if(position.move != 0) {
                    if(position.type == "move"){
                      document.getElementById(blue).innerHTML = "<div class='whiteDot'></div>";
                      document.getElementById(blue).classList.remove('player');
                      document.getElementById(position.move).innerHTML = "<div class='blueDot'></div>";
                      document.getElementById(position.move).classList.add('player');
                      blue = position.move;
                    }
                    else if (position.type == "hammer") {
                      document.getElementById(position.move).innerHTML = "<div class='closes'></div>";
                      document.getElementById(position.move).classList.remove('dotBorder');
                      document.getElementById(position.move).classList.add('closed');
                    }
                  }
                }
                if(position.key == 'red'){
                  if(position.move != 0) {
                    if(position.type == "move"){
                      document.getElementById(red).innerHTML = "<div class='whiteDot'></div>";
                      document.getElementById(red).classList.remove('player');
                      document.getElementById(position.move).innerHTML = "<div class='redDot'></div>";
                      document.getElementById(position.move).classList.add('player');
                      red = position.move;
                    }
                    else if (position.type == "hammer") {
                      document.getElementById(position.move).innerHTML = "<div class='closes'></div>";
                      document.getElementById(position.move).classList.remove('dotBorder');
                      document.getElementById(position.move).classList.add('closed');
                    }
                  }
                }
                if(position.key == 'green'){
                  if(position.move != 0) {
                    if(position.type == "move"){
                      document.getElementById(green).innerHTML = "<div class='whiteDot'></div>";
                      document.getElementById(green).classList.remove('player');
                      document.getElementById(position.move).innerHTML = "<div class='greenDot'></div>";
                      document.getElementById(position.move).classList.add('player');
                      green = position.move;
                    }
                    else if (position.type == "hammer") {
                      document.getElementById(position.move).innerHTML = "<div class='closes'></div>";
                      document.getElementById(position.move).classList.remove('dotBorder');
                      document.getElementById(position.move).classList.add('closed');
                    }
                  }
                }

              });
              // emit to resolve all moves and empty positions object
              this.current.red = red;
              this.current.blue = blue;
              this.current.green = green;

              this.positions = [];

              sessionStorage.setItem('turn',false);
              this.activeBtn = '';

              // end of game
              this.form.turns = this.form.turns - 1;
              console.log(this.form.turns);

              if (this.form.turns == 0) {
                // lets get the current positions
                var b = 0;
                var r = 0;
                var g = 0;

                // calculate scores
                b = this.checkwins(this.current.blue);
                r = this.checkwins(this.current.red);
                g = this.checkwins(this.current.green);

                if (b > r && b > g) {
                  alert('Blue Wins!');
                }
                else if (r > b && r > g) {
                  alert('Red Wins!');
                }
                else if (g > b && g > r) {
                    alert('Green Wins!');
                }
                else if (b == r && b > g) {
                    alert('Blue and Red Wins!');
                }
                else if (g == r && g > b) {
                    alert('Green and Red Wins!');
                }
                else{
                    alert('Nobody won.');
                }

                alert('GAME OVER! SCORE BOARD: red - ' + r + ' blue - ' + b + ' green - ' + g);
              }
            }
          });

          this.socket.on("incomplete", data => {
            // alert when a player quits
            alert(data);
            this.isHidden = false;
            this.isDisabled = true;
            this.form.units = 4;
            this.form.turns = 1;
            this.update(1, false);
            this.update(2, false);
            this.update(3, false);
          });


          // --- helper to sort

          if( typeof helper == 'undefined' ) {
            var helper = { } ;
          }

          helper.arr = {
                   /**
               * Function to sort multidimensional array
               *
               * <a href="/param">@param</a> {array} [arr] Source array
               * <a href="/param">@param</a> {array} [columns] List of columns to sort
               * <a href="/param">@param</a> {array} [order_by] List of directions (ASC, DESC)
               * @returns {array}
               */
              multisort: function(arr, columns, order_by) {
                  if(typeof columns == 'undefined') {
                      columns = []
                      for(x=0;x<arr[0].length;x++) {
                          columns.push(x);
                      }
                  }

                  if(typeof order_by == 'undefined') {
                      order_by = []
                      for(x=0;x<arr[0].length;x++) {
                          order_by.push('ASC');
                      }
                  }

                  function multisort_recursive(a,b,columns,order_by,index) {
                      var direction = order_by[index] == 'DESC' ? 1 : 0;

                      var is_numeric = !isNaN(+a[columns[index]] - +b[columns[index]]);


                      var x = is_numeric ? +a[columns[index]] : a[columns[index]].toLowerCase();
                      var y = is_numeric ? +b[columns[index]] : b[columns[index]].toLowerCase();



                      if(x < y) {
                              return direction == 0 ? -1 : 1;
                      }

                      if(x == y)  {
                          return columns.length-1 > index ? multisort_recursive(a,b,columns,order_by,index+1) : 0;
                      }

                      return direction == 0 ? 1 : -1;
                  }

                  return arr.sort(function (a,b) {
                      return multisort_recursive(a,b,columns,order_by,0);
                  });
              }
          };
        },
        methods: {
            forceRerender() {
              this.componentKey += 1;
            },
            startGame() {
              // initiate start match with default var values
              this.form.units = this.form.unitinput;

              this.socket.emit("hidden", this.form.units);
              this.socket.emit("turn", this.form.turns);
            },
            divclick(unit,index){

              if(this.activeBtn == '' && (unit != 0 && index != 0)) {
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
            checkwins(tmp){
              var score = 0;

              tmp = parseFloat(tmp);
              var id1 = String(( tmp - 1.1 ).toFixed(1));
              var id2 = String(( tmp - 1.0 ).toFixed(1));
              var id3 = String(( tmp + 1.1 ).toFixed(1));
              var id4 = String(( tmp + 1.0 ).toFixed(1));
              var id5 = String(( tmp + 0.1 ).toFixed(1));
              var id6 = String(( tmp - 0.1 ).toFixed(1));

              if(document.getElementById(id1)) {
                if ((document.getElementById(id1).className != 'closed') && (document.getElementById(id1).className != 'dotBorder player')) {
                  score++;
                }
                else if (document.getElementById(id1).className == 'dotBorder player'){
                  return 0;
                }
              }

              if(document.getElementById(id2)) {
                if ((document.getElementById(id2).className != 'closed') && (document.getElementById(id2).className != 'dotBorder player')) {
                  score++;
                }
                else if (document.getElementById(id2).className == 'dotBorder player'){
                  return 0;
                }
              }

              if(document.getElementById(id3)) {
                if ((document.getElementById(id3).className != 'closed') && (document.getElementById(id3).className != 'dotBorder player')) {
                  score++;
                }
                else if (document.getElementById(id3).className == 'dotBorder player'){
                  return 0;
                }
              }

              if(document.getElementById(id4)) {
                if ((document.getElementById(id4).className != 'closed') && (document.getElementById(id4).className != 'dotBorder player')) {
                  score++;
                }
                else if (document.getElementById(id4).className == 'dotBorder player'){
                  return 0;
                }
              }

              if(document.getElementById(id5)) {
                if ((document.getElementById(id5).className != 'closed') && (document.getElementById(id5).className != 'dotBorder player')) {
                  score++;
                }
                else if (document.getElementById(id5).className == 'dotBorder player'){
                  return 0;
                }
              }

              if(document.getElementById(id6)) {
                if ((document.getElementById(id6).className != 'closed') && (document.getElementById(id6).className != 'dotBorder player')) {
                  score++;
                }
                else if (document.getElementById(id6).className == 'dotBorder player'){
                  return 0;
                }
              }

              return score;

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
                // set turn to true to identify that the player has already done a move
                sessionStorage.setItem('turn',true);

                // lets get the current positions
                if(this.myKey == 'blue'){
                  tmp = parseFloat(this.current.blue);
                }
                if(this.myKey == 'red'){
                  tmp = parseFloat(this.current.red);
                }
                if(this.myKey == 'green'){
                  tmp = parseFloat(this.current.green);
                }


                // only allow moves that are adjacent
                switch(id) {
                  case ( tmp - 1.1 ).toFixed(1):
                  console.log(tmp + '-1.1' + (tmp - 1.1));
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
                  case '0.0':
                  console.log('skipped');
                    id = 0;
                    this.move_type = 'move';
                    this.isvalid = true;
                    break;
                  default:
                    this.isvalid = false;
                }

                // figure out what type of move before pushing
                if (this.activeBtn == 'btn1') {
                  // hammer move
                  this.move_type = 'hammer';
                } else {
                  // cell movement
                  this.move_type = 'move';
                }


                if (id != 0) {
                  // make sure the move is not hammered
                  if (document.getElementById(id).className == 'closed') {
                    this.isvalid = false;
                  }

                  // make sure that you cannot hammer on an oppising player's position
                  if (document.getElementById(id).className == 'dotBorder player' && this.move_type == 'hammer') {
                    this.isvalid = false;
                  }
                }


                // resolve moves
                if (this.isvalid != false)
                {
                  this.positions.push({"key":this.myKey, "move":id, "type":this.move_type});
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
