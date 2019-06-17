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
          <button class="btn btn-success btn-lg btn-block" v-on:click="changeUnit()" :disabled='isDisabled'>Start Game</button>
        </div>

      </div>
    </div>

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
  </section>
</template>

<script>
    import io from "socket.io-client";
    export default {
        data() {
            return {
                socket: {},
                context: {},
                form: {
                  unitinput: 4,
                  units: 0,
                  turns: 0
                },
                componentKey: 0,
                isHidden: false,
                isDisabled: true
            }
        },
        created() {
          this.socket = io("http://localhost:3000");
        },
        mounted() {
          console.log('Component mounted.');
          this.socket.on("clients", data => {
            console.log('Connections:', data)
            if (data == 3){
              this.isDisabled = false;
            }
          });
        },
        methods: {
            forceRerender() {
              this.componentKey += 1;
            },
            changeUnit() {
              this.form.units = this.form.unitinput;
              this.isHidden = true;
              this.socket.emit("hidden", 3);
            },
            divclick(unit,index){
              console.log('div clicked: '+unit+' , '+index);
              this.$emit('div clicked', 'someValue');
            },
        }
    }
</script>

<style scoped></style>
