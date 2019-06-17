<template>
  <section class="section">
  <form>
    <h1 class="title is-1">
      {{form.formName}}
    </h1>

    <div class="field">
      <label class="label">Name</label>
      <div class="control">
        <input class="input" type="text" v-model="form.userName" />
      </div>
    </div>

    <div class="field">
      <label class="label">Favorite Color</label>
      <div class="control">
        <div class="select">
          <select v-model="form.favoriteColor">
            <option v-for="color in ['Red', 'Blue', 'Green']" :value="color">
              {{color}}
            </option>
          </select>
        </div>
      </div>
    </div>

    <div class="field">
      <label class="label">Favorite Hamburger Place</label>
      <div class="control">
        <label>
          <input type="radio" value="whataburger" v-model="form.favoriteHamburger" />
          Whataburger
        </label>
        <br>
        <label>
          <input type="radio" value="pterrys" v-model="form.favoriteHamburger" />
          P.Terry's
        </label>
        <br>
        <label>
          <input type="radio" value="hopdoddy" v-model="form.favoriteHamburger" />
          Hopdoddy
        </label>
      </div>
    </div>

    <div class="field">
      <label class="label">Favorite Hangout</label>
      <div class="control">
        <label>
          <input type="checkbox" value="barton-springs" v-model="form.favoriteHangout" />
          Barton Springs
        </label>
        <br>
        <label>
          <input type="checkbox" value="zilker-park" v-model="form.favoriteHangout" />
          Zilker Park
        </label>
        <br>
        <label>
          <input type="checkbox" value="sixth-stree" v-model="form.favoriteHangout" />
          Sixth Street
        </label>
      </div>
    </div>

    <div class="field">
      <label class="label">How many hours a week do you work?</label>
      <div class="control">
        <h4 class="title is-4">
          {{form.workHours}} / 80
        </h4>
        <input type="range" min="0" max="80" v-model="form.workHours"/>
      </div>
    </div>

    <input class="button is-primary margin-bottom" type="submit" @click.prevent="fakeSubmit" />

  </form>

  <transition name="fade" mode="out-in">
    <article class="message is-primary" v-show="showSubmitFeedback">
      <div class="message-header">
        <p>Fake Send Status:</p>
      </div>
      <div class="message-body">
        Successfully Submitted!
      </div>
    </article>
  </transition>

  <hr>

  <h5>
    JSON
  </h5>
  <pre><code>{{form}}</code></pre>
  </section>
</template>

<script>
    export default {
      data() {
          return {
            form: {
              formName: 'Tell Us About Yourself',
              userName: '',
              favoriteColor: 'Red',
              favoriteHamburger: '',
              favoriteHangout: [],
              workHours: 0
            },
            showSubmitFeedback: false
          }
      },
      methods: {
        fakeSubmit(){
          this.showSubmitFeedback = true;
          setTimeout(() => {
            this.showSubmitFeedback = false;
          }, 3000);
        }
      },
      mounted() {
          console.log('Component mounted.')
      }
    }
</script>
