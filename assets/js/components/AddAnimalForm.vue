<template>
  <div class="bg-secondary p-8 rounded-lg shadow-lg w-7/12">
    <h2 class="text-2xl font-semibold text-center mb-6">Ajouter un animal</h2>
    <form class="flex flex-col justify-between" @submit.prevent="addAnimal">
      <div class="flex justify-between">
        <div class="animal-picture w-2/5 bg-primary rounded-3xl" :style="{
          backgroundImage: 'url(' + newAnimal.images[0] + ')',
          backgroundSize: 'cover',
          backgroundPosition: 'center'
        }">
          <div class="overlay bg-opacity-50 p-2">
            <input type="file" @change="newOnFileChange($event)" multiple>
          </div>
        </div>
        <div class="w-5/12">
          <div>
            <label for="name">Nom</label>
            <input required v-model="newAnimal.name" type="text" id="name" class="animal-input" />
          </div>
          <label for="type">Type</label>
          <select required v-model="newAnimal.type" id="type" class="animal-input" @change="fetchBreeds(newAnimal.type)">
            <option value="" disabled>Sélectionnez un type</option>
            <option v-for="type in types" :key="type.id" :value="type.id">
              {{ type.name }}
            </option>
          </select>
  
          <div v-if="isTypeSelected">
            <label for="breed">Race</label>
            <select required v-model="newAnimal.breed" id="breed" class="animal-input">
              <option value="" disabled>Sélectionnez une race</option>
              <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                {{ breed.name }}
              </option>
            </select>
          </div>
  
          <div>
            <label for="age">Âge</label>
            <input required v-model="newAnimal.age" type="number" min="1" id="age" class="animal-input" />
          </div>
  
          <div>
            <label for="description">Description</label>
            <textarea required v-model="newAnimal.description" id="description" class="animal-input"></textarea>
          </div>
  
          <div>
            <label for="price">Prix</label>
            <input required v-model="newAnimal.price" type="number" min="0" id="price" class="animal-input" />
          </div>
        </div>
      </div>
  
      <div class="flex justify-between items-end mt-5">
        <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none" @click="$emit('close')">Retour</button>
        <button type="submit" class="p-4 bg-primary font-semibold rounded-md focus:outline-none">
          Ajouter l'animal
        </button>
      </div>
    </form>
  </div>
</template>
<script>
export default {
  name: 'AddAnimalForm',
  props: {
    types: Array,
    breeds: Array,
  },
  emits: ['close', 'addAnimal', 'fetchBreeds'],
  data() {
    return {
      newAnimal: {
        type: '',
        name: '',
        breed: '',
        age: '',
        description: '',
        price: '',
        files: [],
        images: []
      },
      isTypeSelected: false,
    };
  },
  methods: {
    newOnFileChange(e) {
      if (e.target.files && e.target.files.length > 0) {
        const files = e.target.files;

        for (let i = 0; i < files.length; i++) {
          this.newAnimal.files.push(files[i]);
          const imageUrl = URL.createObjectURL(files[i]);
          this.newAnimal.images.push(imageUrl);
        }
      }
    },

    fetchBreeds(typeId) {
      this.isTypeSelected = true;
      this.$emit('fetchBreeds', typeId);
    },

    addAnimal() {
      this.$emit('addAnimal', this.newAnimal);
      this.newAnimal = {
        type: '',
        name: '',
        breed: '',
        age: '',
        description: '',
        price: '',
        files: [],
        images: []
      };
    }
  }
};
</script>