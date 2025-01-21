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
              <option value="addBreed">
                -- Nouvelle race --
              </option>
            </select>
          </div>
          <div v-if="newAnimal.breed === 'addBreed'">
            <label>Ajouter une race</label>
            <input required v-model="newBreed.name" type="text" id="name" class="animal-input" />
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

      <div class="flex justify-between items-end mt-5 text-secondary">
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
  emits: ['close', 'fetchBreeds', 'fetchAnimals', 'update:showAddForm', 'update:isPopupVisible', 'addBreed'],
  data() {
    return {
      addBreed: false,
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
      newBreed: {
        typeId: null,
        name: ''
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

      const base64Files = await Promise.all(this.newAnimal.files.map(file => {
        return new Promise((resolve, reject) => {
          const reader = new FileReader();
          reader.onloadend = () => resolve(reader.result.split(',')[1]);
          reader.onerror = reject;
          reader.readAsDataURL(file);
        });
      }));


      if (this.newAnimal.breed === "addBreed") {
        try {
          const newBreedResult = await new Promise((resolve, reject) => {
            this.$emit('addBreed', this.newAnimal.type, this.newBreed.name, resolve, reject);
          });
          if (newBreedResult) {
            this.newAnimal.breed = newBreedResult.id;
          } else {
            throw new Error('Impossible d\'obtenir l\'ID de la nouvelle race');
          }
        } catch (error) {
          alert(`Erreur lors de l'ajout de la nouvelle race : ${error.message}`);
          return;
        }
      }

      try {
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
          const result = await response.json();
          this.$emit('fetchAnimals', this.actualTypeId);
          this.$emit('update:showAddForm', false);
          this.$emit('update:isPopupVisible', false);
        } else {
          const errorData = await response.text();
          console.error('Erreur lors de l\'ajout de l\'animal :', errorData);
        }
      } catch (error) {
        console.error('Erreur lors de l\'opération fetch :', error);
      }
    }
  }
};
</script>
