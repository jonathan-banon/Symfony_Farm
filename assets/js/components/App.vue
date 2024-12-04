<template>
    <div>
      <div class="bg-secondary min-h-52 flex justify-around items-center">
        <img src="/images/logo.svg" alt="Logo">
        <div class="nav-container">
          <button
            v-for="type in types"
            :key="type.id"
            class="btn bg-primary text-white py-2 px-4 rounded-full"
            @click="fetchAnimals(type.id)"
          >
            {{ type.name }}
          </button>
        </div>
        <a
          :href="isUserLoggedIn ? '/logout' : '/login'"
          class="btn bg-primary text-white py-2 px-4 rounded-full"
        >
          {{ isUserLoggedIn ? 'Déconnexion' : 'Connexion' }}
        </a>
      </div>
  
      <div id="animals-list">
        <ul>
          <li v-for="animal in animals" :key="animal.id" class="animal-item">
            {{ animal.name }}
          </li>
        </ul>
        <p v-if="animals.length === 0">Aucun animal trouvé pour ce type.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        types: [], // Liste des types d'animaux
        animals: [], // Liste des animaux à afficher
        isUserLoggedIn: false, // État de connexion de l'utilisateur
      };
    },
    mounted() {
      // Récupération des données passées depuis Twig dans les attributs data-*
      const appElement = document.getElementById('app');
      const typesData = appElement.getAttribute('data-types');
      const userLoggedIn = appElement.getAttribute('data-user-logged-in');
  
      // Initialisation des données
      this.types = JSON.parse(typesData);
      this.isUserLoggedIn = userLoggedIn === 'true';
    },
    methods: {
      async fetchAnimals(typeId) {
        try {
          // Récupérer les animaux en fonction du type
          const response = await fetch(`/types/animals/${typeId}`);
          const data = await response.json();
          this.animals = data;
        } catch (error) {
          console.error('Erreur lors de la récupération des animaux:', error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .btn {
    font-size: 1.5rem;
    padding: 15px;
    border-radius: 20px;
    width: fit-content;
    height: fit-content;
  }
  
  .nav-container {
    display: flex;
    min-width: 50%;
    justify-content: space-around;
    align-items: end;
  }
  
  #animals-list {
    margin-top: 20px;
  }
  
  .animal-item {
    padding: 5px;
    margin: 5px 0;
    background-color: #f1f1f1;
    border-radius: 5px;
  }
  </style>
  