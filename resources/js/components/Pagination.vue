<template>
  <nav aria-label="Car pagination" v-if="pagination.totalPages > 1">
    <ul class="pagination justify-content-center mb-0">
      <!-- Previous Button -->
      <li class="page-item" :class="{ disabled: !pagination.hasPrev }">
        <button
          class="page-link"
          @click="changePage(pagination.currentPage - 1)"
          :disabled="!pagination.hasPrev"
          aria-label="Previous page"
        >
          <span aria-hidden="true">&laquo;</span>
        </button>
      </li>

      <!-- First Page -->
      <li v-if="showFirstPage" class="page-item" :class="{ active: pagination.currentPage === 1 }">
        <button class="page-link" @click="changePage(1)">1</button>
      </li>

      <!-- Ellipsis after first page -->
      <li v-if="showStartEllipsis" class="page-item disabled">
        <span class="page-link">...</span>
      </li>

      <!-- Page Numbers -->
      <li
        v-for="page in visiblePages"
        :key="page"
        class="page-item"
        :class="{ active: pagination.currentPage === page }"
      >
        <button class="page-link" @click="changePage(page)">{{ page }}</button>
      </li>

      <!-- Ellipsis before last page -->
      <li v-if="showEndEllipsis" class="page-item disabled">
        <span class="page-link">...</span>
      </li>

      <!-- Last Page -->
      <li v-if="showLastPage" class="page-item" :class="{ active: pagination.currentPage === pagination.totalPages }">
        <button class="page-link" @click="changePage(pagination.totalPages)">{{ pagination.totalPages }}</button>
      </li>

      <!-- Next Button -->
      <li class="page-item" :class="{ disabled: !pagination.hasNext }">
        <button
          class="page-link"
          @click="changePage(pagination.currentPage + 1)"
          :disabled="!pagination.hasNext"
          aria-label="Next page"
        >
          <span aria-hidden="true">&raquo;</span>
        </button>
      </li>
    </ul>

    <!-- Page Info -->
    <div class="d-flex justify-content-center mt-3">
      <small class="text-muted">
        Showing {{ startItem }} to {{ endItem }} of {{ pagination.totalItems }} cars
      </small>
    </div>
  </nav>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    pagination: {
      type: Object,
      required: true,
      default: () => ({
        currentPage: 1,
        totalPages: 1,
        totalItems: 0,
        itemsPerPage: 30,
        hasNext: false,
        hasPrev: false
      })
    }
  },
  emits: ['page-changed'],
  computed: {
    visiblePages() {
      const { currentPage, totalPages } = this.pagination;
      const delta = 2; // Number of pages to show on each side of current page
      const range = [];
      const rangeWithDots = [];

      for (
        let i = Math.max(2, currentPage - delta);
        i <= Math.min(totalPages - 1, currentPage + delta);
        i++
      ) {
        range.push(i);
      }
      return range;
    },

    showFirstPage() {
      return this.pagination.totalPages > 1;
    },

    showLastPage() {
      return this.pagination.totalPages > 1 ;
    },

    showStartEllipsis() {
      return this.pagination.currentPage > 4;
    },

    showEndEllipsis() {
      return this.pagination.currentPage < this.pagination.totalPages - 3;
    },

    startItem() {
      return (this.pagination.currentPage - 1) * this.pagination.itemsPerPage + 1;
    },

    endItem() {
      const end = this.pagination.currentPage * this.pagination.itemsPerPage;
      return Math.min(end, this.pagination.totalItems);
    }
  },
  methods: {
    changePage(page) {
      if (page >= 1 && page <= this.pagination.totalPages && page !== this.pagination.currentPage) {
        this.$emit('page-changed', page);
      }
    }
  }
};
</script>
