<template>
    <div class="filter-container bg-gray-100 rounded-lg p-5 w-80 space-y-4 mr-5">
        <div
            class="search-bar-container border-b-2 border-b-greyCustom flex justify-around items-center justify-center">
            <div class="w-1/3 h-4/6 border-r-2 border-r-greyCustom flex items-center p-3">
                <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                    'bg-primary text-secondary': selectedStatus === 'all',
                    'bg-transparent text-primary-500': selectedStatus !== 'all'
                }" @click="selectStatus('all')">
                    Tout les animaux
                </button>
            </div>
            <div class="w-1/3 h-4/6 border-r-2 border-r-greyCustom flex items-center justify-center">
                <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                    'bg-primary text-secondary': selectedStatus === 'onSale',
                    'bg-transparent text-primary-500': selectedStatus !== 'onSale'
                }" @click="selectStatus('onSale')">
                    À vendre
                </button>
            </div>
            <div class="w-1/3 h-4/6 flex items-center justify-center">
                <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                    'bg-primary text-secondary': selectedStatus === 'sale',
                    'bg-transparent text-primary-500': selectedStatus !== 'sale'
                }" @click="selectStatus('sale')">
                    Vendu
                </button>
            </div>
        </div>
        <div class="search-bar-container border-b-2 border-b-greyCustom">
            <input type="text" v-model="searchVal" placeholder="Recherche ..."
                class="w-full p-2 rounded-2xl border-2 border-black-500 bg-fillGrey" />
        </div>
        <div class="h-1/5 border-b-2 border-b-greyCustom">
            <p class="text-base">Races</p>
            <div class="flex gap-3 mt-2">
                <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                    'bg-primary text-secondary': selectedBreed === null,
                    'bg-transparent text-primary-500': selectedBreed !== null
                }" @click="selectBreedId(null)">
                    Toutes les races
                </button>
                <template v-for="breed in filteredBreeds" :key="breed.id">
                    <button class="text-xs border border-primary-500 p-2 rounded-full" :class="{
                        'bg-primary text-secondary': selectedBreed === breed.name,
                        'bg-transparent text-primary-500': selectedBreed !== breed.name
                    }" @click="selectBreedId(breed.name)">
                        {{ breed.name }}
                    </button>
                </template>
                <img @click="toggleBreedForm()" :src="addLogo" alt="add-logo" class="w-6">
            </div>
        </div>
        <div class="h-1/5 border-b-2 border-b-greyCustom">
            <p class="text-base">Prix</p>
            <vue-slider v-model="sliderValues" :dot-style="{ backgroundColor: '#281709' }"
                :process-style="{ backgroundColor: '#582D09' }" :use-range="true" :enable-cross="false" :max="value[1]"
                :min="value[0]"></vue-slider>
            <div class="flex justify-between mt-4 text-xs">
                <div class="w-fit">
                    <p>Minimum</p>
                    <div class="price-container">
                        {{ sliderValues[0] }} €
                    </div>
                </div>
                <div>
                    <p>Maximum</p>
                    <div class="price-container">
                        {{ sliderValues[1] }} €
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p v-if="displayedAnimals.length != 0" class="text-xs mb-5 mt-5">{{ displayedAnimals.length }} Résultat{{
        displayedAnimals.length === 1 ? '' : 's' }} trouvé{{ displayedAnimals.length === 1 ? '' : 's' }}</p>
    <div class="flex items-center gap-2 cursor-pointer relative mb-5 w-1/3" @click="toggleSortMenu">
        <div class="relative flex items-center justify-between p-2 border border-gray-300 rounded-2xl w-full">
            <div class="flex">
                <img :src="logoSortBar1" class="w-4 mr-2" />
                <p class="text-sm">{{ getSortLabel }}</p>
            </div>
            <img :src="logoSortBar2" class="w-4 ml-2" />
        </div>
    </div>
    <template v-if="sortMenuOpen"
        class="absolute bg-white border border-gray-300 p-2 rounded-md w-48 mt-2 display-sortBar">
        <div class="bg-gray-200 p-2 mb-2 rounded-md absolute z-50 bg-secondary">
            <div v-for="option in sortOptions" :key="option.value"
                class="flex items-center py-1 px-2 cursor-pointer hover:bg-gray-300"
                @click="selectSortOrder(option.value)">
                <p class="text-sm">{{ option.label }}</p>
            </div>
        </div>
    </template>
    <div class="cursor-pointer h-2/6 flex justify-center items-center border-dashed border-2 border-sky-500 mb-6" @click="toggleAddAnimalForm()">
        <p class="text-4xl">Ajouter un animal</p>
    </div>
    <div v-for="animal in displayedAnimals" :key="animal.id" class="animal-item rounded-3xl h-1/2 flex">
        <form class="flex justify-between w-full" @submit.prevent="editAnimal(animal)" enctype="multipart/form-data">
            <div class="animal-picture w-1/4 bg-primary rounded-3xl" :style="{
                backgroundImage: 'url(' + animal.images[animal.currentImageIndex] + ')',
                backgroundSize: 'cover',
                backgroundPosition: 'center'
            }">
                <div class="flex justify-end p-3">
                    <a @click.prevent="delImage(animal)">
                        <img :src="trashUrl" alt="trash-logo" class="w-8 cursor-pointer">
                    </a>
                </div>
                <div class="carousel-container" v-if="animal.images.length > 1">
                    <img :src="leftArrow" class="carousel-btn top-30 left-1 w-10" @click="prevImage(animal)">
                    <img :src="rightArrow" class="carousel-btn top-30 right-1 w-10" @click="nextImage(animal)">
                </div>

                <div class="overlay bg-opacity-50 p-2">
                    <input type="file" @change="onFileChange($event, animal)" multiple>
                    <button @click="uploadImage(animal)">Télécharger</button>
                </div>
            </div>
            <div>
                <div>
                    <label for="name">Nom</label>
                    <input v-model="animal.name" type="text" id="name" class="animal-input"
                        @input="trackFormChanges(animal)" />
                </div>

                <label for="breed">Race</label>
                <select v-model="animal.breed" id="breed" class="animal-input" @change="trackFormChanges(animal)">
                    <option :value="animal.breed" disabled>
                        {{ animal.breed }}
                    </option>
                    <option v-for="breed in breeds" :key="breed.id" :value="breed.id">
                        {{ breed.name }}
                    </option>
                </select>
                <img src="" alt="">

                <div>
                    <label for="description">Description</label>
                    <textarea v-model="animal.description" id="description" class="animal-input"
                        @input="trackFormChanges(animal)"></textarea>
                </div>
            </div>
            <div>
                <div>
                    <label for="age">Âge</label>
                    <input v-model="animal.age" type="number" id="age" class="animal-input"
                        @input="trackFormChanges(animal)" />
                </div>

                <div>
                    <label for="price">Prix</label>
                    <input v-model="animal.price" type="number" id="price" class="animal-input"
                        @input="trackFormChanges(animal)" />
                </div>
                <label for="isOnSale">Status</label>
                <div>
                    <div>
                        <input type="radio" v-model="animal.isOnSale" :value=true @change="trackFormChanges(animal)" />
                        <span class="ml-2">En vente</span>
                    </div>
                    <div>
                        <input type="radio" v-model="animal.isOnSale" :value=false @change="trackFormChanges(animal)" />
                        <span class="ml-2">Vendu</span>
                    </div>
                </div>
            </div>
            <div class="flex flex-col justify-between items-end">
                <a @click.prevent="delAnimal(animal)" class="mt-3 mr-3">
                    <img :src="trashUrl" alt="trash-logo" class="w-8 cursor-pointer">
                </a>
                <button type="submit" class="p-4 font-semibold rounded-md focus:outline-none" :class="{
                    'bg-transparent text-primary-500': !formChanged[animal.id],
                    'bg-primary text-secondary': formChanged[animal.id],
                    'cursor-not-allowed opacity-50': !formChanged[animal.id]
                }" :disabled="!formChanged[animal.id]">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</template>
<script>
import VueSlider from 'vue-slider-component';
import 'vue-slider-component/theme/default.css';
import rightArrow from '../../images/right-arrow.svg';
import leftArrow from '../../images/left-arrow.svg';
import addLogo from '../../images/add.svg';
import logoSortBar1 from '../../images/logoSortBar1.svg';
import logoSortBar2 from '../../images/logoSortBar2.svg';
import addAnimal from '../../images/addAnimal.svg';


export default {
    components: {
        VueSlider
    },
    data() {
        return {
            selectedStatus: "all",
            leftArrow: leftArrow,
            rightArrow: rightArrow,
            searchVal: '',
            selectedBreed: null,
            sliderValues: [0, 0],
            formChanged: {},
            temporaryAnimals: {},
            addLogo: addLogo,
            sortMenuOpen: false,
            logoSortBar1: logoSortBar1,
            logoSortBar2: logoSortBar2,
            addAnimal: addAnimal,
            sortOrder: 'alpha-asc',
            sortOptions: [
                { value: 'price-asc', label: 'Trier par prix croissant' },
                { value: 'price-desc', label: 'Trier par prix décroissant' },
                { value: 'alpha-asc', label: 'Trier par ordre alphabétique croissant' },
                { value: 'alpha-desc', label: 'Trier par ordre alphabétique décroissant' },
                { value: 'age-asc', label: 'Trier par âge croissant' },
                { value: 'age-desc', label: 'Trier par âge décroissant' },
            ],
        }

    },
    computed: {
        displayedAnimals() {
            let filtered = this.getVisibleAnimals();
            filtered = filtered.filter(
                (animal) => animal.price >= this.sliderValues[0] && animal.price <= this.sliderValues[1]
            );

            return filtered.slice().sort(this.getSortFunction());
        },
        getSortLabel() {
            const option = this.sortOptions.find(opt => opt.value === this.sortOrder);
            return option ? option.label : '';
        },
        filteredBreeds() {
            return this.breeds.filter((breed) =>
                this.animals.some((animal) => animal.breed === breed.name)
            );
        },
        value: {
            get() {
                if (this.animals.length === 0) return [0, 1000];
                const prices = this.animals.map(animal => animal.price).filter(price => price !== undefined);
                if (prices.length === 0) return [0, 1000];
                return [Math.min(...prices), Math.max(...prices)];
            },
            set(newValue) {
                this.sliderValues = newValue;
            },
        },
    },
    props: {
        animals: Array,
        breeds: Array,
        trashUrl: String,
        actualTypeId: Number
    },
    emits: ['prev-image', 'next-image', 'del-animal', 'update:animals', 'fetchAnimals', 'animal-updated', 'toggleBreedForm', 'toggleAddAnimalForm'],
    methods: {
        toggleAddAnimalForm() {
            this.$emit('toggleAddAnimalForm');
        },
        selectSortOrder(value) {
            this.sortOrder = value;
            this.sortMenuOpen = false;
        },
        toggleSortMenu() {
            this.sortMenuOpen = !this.sortMenuOpen;
        },
        selectStatus(status) {
            this.selectedStatus = status
        },
        getVisibleAnimals() {
            let filtered = [...this.animals];

            filtered = filtered.filter(animal => !this.temporaryAnimals[animal.id]);

            if (this.selectedStatus === 'onSale') {
                filtered = filtered.filter((animal) => animal.isOnSale === true || animal.isOnSale === "true");
            } else if (this.selectedStatus === 'sale') {
                filtered = filtered.filter((animal) => animal.isOnSale === false || animal.isOnSale === "false");
            }

            if (this.searchVal) {
                const searchLower = this.searchVal.toLowerCase();
                filtered = filtered.filter((animal) =>
                    animal.name.toLowerCase().includes(searchLower)
                );
            }

            if (this.selectedBreed) {
                filtered = filtered.filter(
                    (animal) => animal.breed === this.selectedBreed
                );
            }

            return filtered;
        },
        toggleBreedForm() {
            this.$emit('toggleBreedForm');
        },
        getSortFunction() {
            switch (this.sortOrder) {
                case 'price-asc':
                    return (a, b) => a.price - b.price;
                case 'price-desc':
                    return (a, b) => b.price - a.price;
                case 'alpha-asc':
                    return (a, b) => a.name.localeCompare(b.name);
                case 'alpha-desc':
                    return (a, b) => b.name.localeCompare(a.name);
                case 'age-asc':
                    return (a, b) => a.age - b.age;
                case 'age-desc':
                    return (a, b) => b.age - a.age;
                default:
                    return () => 0;
            }
        },
        selectBreedId(breedName) {
            this.selectedBreed = breedName;
        },
        async editAnimal(animal) {
            try {
                this.temporaryAnimals[animal.id] = { ...animal };

                const response = await fetch(`/animal/${animal.id}/edit`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        name: animal.name,
                        breed: animal.breed,
                        description: animal.description,
                        price: animal.price,
                        status: animal.isOnSale,
                        age: animal.age
                    }),
                });
                if (response.ok) {
                    this.formChanged[animal.id] = false;
                    this.$emit('animal-updated', animal);
                    delete this.temporaryAnimals[animal.id];
                }
            } catch (error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        async delImage(animal) {
            try {
                const response = await fetch(`/animal/${animal.id}/delete-image/${animal.currentImageIndex}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: animal.id,
                        currentImageIndex: animal.currentImageIndex
                    })

                })
                if (response.ok) {
                    this.$emit('fetchAnimals', this.actualTypeId);
                }
            } catch {

            }
        },
        prevImage(animal) {
            this.$emit('prev-image', animal);
        },
        nextImage(animal) {
            this.$emit('next-image', animal);
        },
        onFileChange(e, animal) {
            const files = e.target.files;
            if (files && files.length > 0) {
                if (!animal.files) {
                    animal.files = [];
                }
                for (let i = 0; i < files.length; i++) {
                    animal.files.push(files[i]);
                    const imageUrl = URL.createObjectURL(files[i]);
                    animal.images.push(imageUrl);
                }
                animal.currentImageIndex = animal.images.length - 1;
            }
        },
        async uploadImage(animal) {
            if (animal.files.length === 0) {
                alert("Veuillez sélectionner un fichier.");
                return;
            }

            const formData = new FormData();
            for (let i = 0; i < animal.files.length; i++) {
                formData.append("images[]", animal.files[i]);
            }
            try {
                const response = await fetch(`/animal/${animal.id}/upload-image`, {
                    method: "POST",
                    body: formData,
                });
                if (response.ok) {
                    const data = await response.json();
                    alert("Image téléchargée avec succès !");
                    animal.previewImage = data.imagePath;
                } else {
                    throw new Error("Échec de l'upload de l'image.");
                }

            } catch (error) {
                console.error("Erreur :", error);
                alert(error.message);
            }
        },
        async delAnimal(animal) {
            try {
                const response = await fetch(`/animal/${animal.id}/del`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        id: animal.id
                    })
                })
                if (response.ok) {
                    this.$emit('update:animals', this.animals.filter(a => a.id !== animal.id))
                } else {
                    console.error('Erreur lors de la suppression de l\'animal');
                }
            } catch {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
            }
        },
        trackFormChanges(animal) {
            this.formChanged[animal.id] = true;
        }
    },
    watch: {
        animals: {
            handler(newAnimals) {
                if (newAnimals.length > 0) {
                    const prices = newAnimals.map((animal) => animal.price);
                    this.sliderValues = [Math.min(...prices), Math.max(...prices)];
                } else {
                    this.sliderValues = [0, 0];
                }
                this.formChanged = Object.fromEntries(newAnimals.map(animal => [animal.id, false]));
            },
            immediate: true
        }
    },
};
</script>

<style>
.search-bar-container {
    height: 10%;
}

.price-container {
    border-radius: 20px;
    box-shadow: 1px 4px 1px #15151514;
    padding: 10px;
    width: fit-content;
    text-align: center;
    width: 100%;
}

.top-30 {
    top: 30% !important;
}
</style>
