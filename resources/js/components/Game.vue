<template>
  <section class="section">
    <div class="container">
      <div>
        <center>
          <div class="container">
            <section id="game">
              <div class="gameboard">
                <div v-for="(units, index) in form.units" class="board2" v-bind:id="units">
                    <div v-for="(unit, index2) in units" v-bind:id="units" v-on:click="divclick(units,index2)">
                      <div class='dotBorder' v-if="units == 1"><div class='blueDot'></div></div>
                      <div class='dotBorder' v-if="units == form.units && index2 == 0"><div class='redDot'></div></div>
                      <div class='dotBorder' v-if="units == form.units && unit == units"><div class='greenDot'></div></div>
                      <div class='dotBorder' v-if="units != form.units && units != 1"><div class='whiteDot'></div></div>
                      <div class='dotBorder' v-if="units != 1 && (units == form.units && index2 != 0) && (units == form.units && unit != units)"><div class='whiteDot'></div></div>
                      <!--<div id="1"><div class='closes' ></div></div>-->
                    </div>
                    <br />
                </div>
              </div>
            </section>
          </div>
        </center>

      </div>
    </div>


    <br /><br />
    <p>
      isHidden: {{ isHidden }}
      isDisabled: {{ isDisabled }}
    </p>

    <center>
      <div>
          <canvas ref="game" width="640" height="480" style="border: 1px solid black;"></canvas>
          <p>
              <button v-on:click="move('right')">Right</button>
              <button v-on:click="move('left')">Left</button>
              <button v-on:click="move('up')">Up</button>
              <button v-on:click="move('down')">Down</button>
          </p>
      </div>
    </center>

  </section>
</template>

<script>
    import io from "socket.io-client";
    export default {
        props: {
          form: {
            type: Object,
            // Object or array defaults must be returned from
            // a factory function
            default: function () {
              return {
                units: 0,
                turns: 0
               }
            }
          },
          isHidden: Boolean,
          isDisabled: Boolean,
        },
        data() {
            return {
                socket: {},
                context: {},
                position: {
                    x: 0,
                    y: 0
                },
            }
        },
        created() {
          this.socket = io("http://localhost:3000");
        },
        mounted() {
          console.log('Component mounted.');
          this.context = this.$refs.game.getContext("2d");
          this.socket.on("position", data => {
              this.position = data;
              this.context.clearRect(0, 0, this.$refs.game.width, this.$refs.game.height);
              this.context.fillStyle = "#FFFFFF";
              this.context.fillRect(0, 0, this.$refs.game.width, this.$refs.game.width);
              this.context.fillStyle = "#000000";
              this.context.fillRect(this.position.x, this.position.y, 20, 20);
          });

          this.socket.on("clients", data => {
            this.$emit('event_child', data);
          });
        },
        methods: {
            move(direction) { this.socket.emit("move", direction); },
            moves(id) {
              console.log('div clicked: '+id);
            },
            divclick(unit,index){
              console.log('div clicked: '+unit+' , '+index);
              this.$emit('div clicked', 'someValue');
            },
        }
    }
</script>

<style scoped></style>
