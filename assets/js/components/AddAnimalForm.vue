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
          <select required v-model="newAnimal.type" id="type" class="animal-input"
            @change="fetchBreeds(newAnimal.type)">
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
        <button class="p-4 bg-primary font-semibold rounded-md focus:outline-none"
          @click="$emit('close')">Retour</button>
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
    actualTypeId: Number,
    showAddForm: Boolean,
    isPopupVisible: Boolean,
  },
  emits: ['close', 'fetchBreeds', 'fetchAnimals', 'update:showAddForm', 'update:isPopupVisible'],
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

    async addAnimal() {
      if (!this.newAnimal.files || this.newAnimal.files.length === 0) {
        alert("Veuillez sélectionner une image pour votre animal.");
        return;
      }

      const base64Files = [];

      for (let i = 0; i < this.newAnimal.files.length; i++) {
        const file = this.newAnimal.files[i];
        const reader = new FileReader();

        const fileBase64 = await new Promise((resolve, reject) => {
          reader.onloadend = () => {
            const base64File = reader.result.split(',')[1];
            resolve(base64File);
          };
          reader.onerror = reject;
          reader.readAsDataURL(file);
        });

        base64Files.push(fileBase64);
      }

      const response = await fetch('/animal/new', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          type: this.newAnimal.type,
          name: this.newAnimal.name,
          breed: this.newAnimal.breed,
          age: this.newAnimal.age,
          description: this.newAnimal.description,
          price: this.newAnimal.price,
          files: base64Files,
        })
      });

      if (response.ok) {
        this.$emit('fetchAnimals', this.actualTypeId);
        this.$emit('update:showAddForm', false);
        this.$emit('update:isPopupVisible', false);
      } else {
        console.error('Erreur lors de l\'ajout de l\'animal');
      }
    }
  }
};
</script>
