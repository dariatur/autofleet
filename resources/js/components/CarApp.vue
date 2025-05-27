<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h2 mb-0">Car Management</h1>
          <button
            class="btn btn-primary"
            @click="showCreateForm"
            :disabled="loading"
          >
            <i class="bi bi-plus-circle me-2"></i>Add New Car
          </button>
        </div>

        <!-- Loading Spinner -->
        <div v-if="loading" class="text-center py-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <!-- Error Alert -->
        <div v-if="error" class="alert alert-danger alert-dismissible" role="alert">
          {{ error }}
          <button type="button" class="btn-close" @click="error = null"></button>
        </div>

        <!-- Success Alert -->
        <div v-if="success" class="alert alert-success alert-dismissible" role="alert">
          {{ success }}
          <button type="button" class="btn-close" @click="success = null"></button>
        </div>

        <!-- Cars Table -->
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead class="table-light">
                  <tr>
                    <th scope="col" class="sortable" @click="sortBy('make')">
                      Make
                      <i class="bi" :class="getSortIcon('make')"></i>
                    </th>
                    <th scope="col" class="sortable" @click="sortBy('model')">
                      Model
                      <i class="bi" :class="getSortIcon('model')"></i>
                    </th>
                    <th scope="col" class="sortable" @click="sortBy('year')">
                      Year
                      <i class="bi" :class="getSortIcon('year')"></i>
                    </th>
                    <th scope="col" class="sortable" @click="sortBy('price')">
                      Price
                      <i class="bi" :class="getSortIcon('price')"></i>
                    </th>
                    <th scope="col" class="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="car in cars" :key="car.id">
                    <td class="fw-medium">{{ car.make }}</td>
                    <td>{{ car.model }}</td>
                    <td>{{ car.year }}</td>
                    <td class="text-success fw-medium">${{ formatPrice(car.price) }}</td>
                    <td class="text-end">
                      <div class="btn-group btn-group-sm">
                        <button
                          class="btn btn-outline-primary"
                          @click="editCar(car)"
                          :disabled="loading"
                        >
                          <i class="bi bi-pencil"></i>
                        </button>
                        <button
                          class="btn btn-outline-danger"
                          @click="confirmDelete(car)"
                          :disabled="loading"
                        >
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  <tr v-if="cars.length === 0 && !loading">
                    <td colspan="5" class="text-center text-muted py-4">
                      No cars found. <a href="#" @click="showCreateForm" class="text-decoration-none">Add the first car</a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Pagination -->
          <div class="card-footer bg-white" v-if="pagination.totalPages > 1">
            <Pagination
              :pagination="pagination"
              @page-changed="loadCarsPage"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Car Form Modal -->
    <div class="modal fade" ref="carModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ isEditing ? 'Edit Car' : 'Add New Car' }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <form @submit.prevent="saveCar">
              <div class="mb-3">
                <label for="make" class="form-label">Make *</label>
                <input
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': formErrors.make }"
                  id="make"
                  v-model="form.make"
                  required
                  placeholder="e.g., Toyota, BMW, Ford"
                >
                <div v-if="formErrors.make" class="invalid-feedback">
                  {{ formErrors.make }}
                </div>
              </div>

              <div class="mb-3">
                <label for="model" class="form-label">Model *</label>
                <input
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': formErrors.model }"
                  id="model"
                  v-model="form.model"
                  required
                  placeholder="e.g., Camry, X5, F-150"
                >
                <div v-if="formErrors.model" class="invalid-feedback">
                  {{ formErrors.model }}
                </div>
              </div>

              <div class="mb-3">
                <label for="year" class="form-label">Year *</label>
                <input
                  type="number"
                  class="form-control"
                  :class="{ 'is-invalid': formErrors.year }"
                  id="year"
                  v-model.number="form.year"
                  required
                  min="2000"
                  max="2025"
                  placeholder="e.g., 2024"
                >
                <div v-if="formErrors.year" class="invalid-feedback">
                  {{ formErrors.year }}
                </div>
              </div>

              <div class="mb-3">
                <label for="price" class="form-label">Price *</label>
                <div class="input-group">
                  <span class="input-group-text">$</span>
                  <input
                    type="number"
                    class="form-control"
                    :class="{ 'is-invalid': formErrors.price }"
                    id="price"
                    v-model.number="form.price"
                    required
                    min="100"
                    placeholder="e.g., 25000"
                  >
                  <div v-if="formErrors.price" class="invalid-feedback">
                    {{ formErrors.price }}
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button
              type="button"
              class="btn btn-primary"
              @click="saveCar"
              :disabled="formLoading"
            >
              <span v-if="formLoading" class="spinner-border spinner-border-sm me-2"></span>
              {{ isEditing ? 'Update Car' : 'Add Car' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" ref="deleteModal" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Confirm Delete</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this car?</p>
            <div v-if="carToDelete" class="card bg-light">
              <div class="card-body">
                <p class="mb-1"><strong>{{ carToDelete.make }} {{ carToDelete.model }}</strong></p>
                <small class="text-muted">{{ carToDelete.year }} • ${{ formatPrice(carToDelete.price) }}</small>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button
              type="button"
              class="btn btn-danger"
              @click="deleteCar"
              :disabled="deleteLoading"
            >
              <span v-if="deleteLoading" class="spinner-border spinner-border-sm me-2"></span>
              Delete Car
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';
import carService from '../services/carService.js';
import Pagination from './Pagination.vue';

export default {
  name: 'CarApp',
  components: {
    Pagination
  },
  data() {
    return {
      cars: [],
      pagination: {
        currentPage: 1,
        totalPages: 1,
        totalItems: 0,
        itemsPerPage: 30,
        hasNext: false,
        hasPrev: false
      },
      sortField: null,
      sortDirection: 'asc',
      loading: false,
      formLoading: false,
      deleteLoading: false,
      error: null,
      success: null,
      isEditing: false,
      carToDelete: null,
      form: {
        id: null,
        make: '',
        model: '',
        year: new Date().getFullYear(),
        price: null
      },
      formErrors: {}
    };
  },
  async mounted() {
    await this.loadCars();
  },
  methods: {
    async loadCars(page = 1) {
      this.loading = true;
      this.error = null;
      try {
        const result = await carService.getCars(page, this.sortField, this.sortDirection);
        this.cars = result.data;
        this.pagination = result.pagination;
      } catch (error) {
        this.error = 'Failed to load cars. Please try again.';
        console.error('Error loading cars:', error);
      } finally {
        this.loading = false;
      }
    },

    async loadCarsPage(page) {
      await this.loadCars(page);
      // Scroll to top of table when changing pages
      this.$el.querySelector('.card').scrollIntoView({ behavior: 'smooth' });
    },

    showCreateForm() {
      this.isEditing = false;
      this.resetForm();
      this.showModal('carModal');
    },

    editCar(car) {
      this.isEditing = true;
      this.form = { ...car };
      this.showModal('carModal');
    },

    async saveCar() {
      this.formLoading = true;
      this.formErrors = {};
      this.error = null;

      // Frontend validation
      if (!this.validateForm()) {
        this.formLoading = false;
        return;
      }

      try {
        if (this.isEditing) {
          await carService.updateCar(this.form.id, {
            make: this.form.make,
            model: this.form.model,
            year: this.form.year,
            price: this.form.price
          });
          this.success = 'Car updated successfully!';
        } else {
          await carService.createCar({
            make: this.form.make,
            model: this.form.model,
            year: this.form.year,
            price: this.form.price
          });
          this.success = 'Car added successfully!';
        }

        this.hideModal('carModal');
        await this.loadCars();
      } catch (error) {
        if (error.response?.data?.violations) {
          error.response.data.violations.forEach(violation => {
            this.formErrors[violation.propertyPath] = violation.message;
          });
        } else {
          this.error = 'Failed to save car. Please try again.';
        }
        console.error('Error saving car:', error);
      } finally {
        this.formLoading = false;
      }
    },

    validateForm() {
      this.formErrors = {};
      let isValid = true;

      // Validate required fields
      if (!this.form.make || this.form.make.trim() === '') {
        this.formErrors.make = 'Make is required';
        isValid = false;
      }

      if (!this.form.model || this.form.model.trim() === '') {
        this.formErrors.model = 'Model is required';
        isValid = false;
      }

      // Validate year - must not be empty and must be a valid number
      if (!this.form.year || this.form.year === '' || this.form.year === null) {
        this.formErrors.year = 'Year is required';
        isValid = false;
      } else if (this.form.year < 2000 || this.form.year > 2025) {
        this.formErrors.year = 'Year must be between 2000 and 2025';
        isValid = false;
      }

      // Validate price - must not be empty and must be a valid number
      if (!this.form.price || this.form.price === '' || this.form.price === null) {
        this.formErrors.price = 'Price is required';
        isValid = false;
      } else if (this.form.price < 100) {
        this.formErrors.price = 'Price must be at least $100';
        isValid = false;
      }

      return isValid;
    },

    confirmDelete(car) {
      this.carToDelete = car;
      this.showModal('deleteModal');
    },

    async deleteCar() {
      this.deleteLoading = true;
      this.error = null;

      try {
        await carService.deleteCar(this.carToDelete.id);
        this.success = 'Car deleted successfully!';
        this.hideModal('deleteModal');
        await this.loadCars();
      } catch (error) {
        this.error = 'Failed to delete car. Please try again.';
        console.error('Error deleting car:', error);
      } finally {
        this.deleteLoading = false;
      }
    },

    resetForm() {
      this.form = {
        id: null,
        make: '',
        model: '',
        year: new Date().getFullYear(),
        price: null
      };
      this.formErrors = {};
    },

    showModal(modalRef) {
      const modal = new Modal(this.$refs[modalRef]);
      modal.show();
    },

    hideModal(modalRef) {
      const modal = Modal.getInstance(this.$refs[modalRef]);
      if (modal) {
        modal.hide();
      }
    },

    formatPrice(price) {
      return new Intl.NumberFormat('en-US').format(price);
    },

    sortBy(field) {
      if (this.sortField === field) {
        // If already sorting by this field, toggle direction
        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
      } else {
        // If sorting by new field, default to ascending
        this.sortField = field;
        this.sortDirection = 'asc';
      }

      // Reset to first page when sorting changes
      this.loadCars(1);
    },

    getSortIcon(field) {
      if (this.sortField !== field) {
        return 'bi-arrow-down-up'; // Default sort icon
      }

      return this.sortDirection === 'asc' ? 'bi-sort-alpha-down' : 'bi-sort-alpha-up';
    }
  }
};
</script>

<style scoped>
.sortable {
  cursor: pointer;
  user-select: none;
  transition: background-color 0.2s ease;
}

.sortable:hover {
  background-color: rgba(0, 0, 0, 0.05);
}

.sortable i {
  margin-left: 0.25rem;
  font-size: 0.8rem;
  color: #6c757d;
}

.sortable:hover i {
  color: #495057;
}
</style>
