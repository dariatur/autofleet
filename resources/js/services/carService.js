import axios from 'axios';

const API_BASE_URL = '/api';

export class CarService {
    async getCars(page = 1) {
        const response = await axios.get(`${API_BASE_URL}/cars?page=${page}`);

        // Extract pagination info from response headers or data structure
        const cars = response.data;
        const totalCount = response.headers['x-total-count'] || cars.length;
        const itemsPerPage = response.headers['x-page-count'];
        const totalPages = Math.ceil(totalCount / itemsPerPage);

        return {
            data: cars,
            pagination: {
                currentPage: page,
                totalPages: totalPages,
                totalItems: totalCount,
                itemsPerPage: itemsPerPage,
                hasNext: page < totalPages,
                hasPrev: page > 1
            }
        };
    }

    async getCar(id) {
        const response = await axios.get(`${API_BASE_URL}/cars/${id}`);
        return response.data;
    }

    async createCar(carData) {
        const response = await axios.post(`${API_BASE_URL}/cars`, carData);
        return response.data;
    }

    async updateCar(id, carData) {
        const response = await axios.patch(`${API_BASE_URL}/cars/${id}`, carData, {
            headers: {
                'Content-Type': 'application/merge-patch+json'
            }
        });
        return response.data;
    }

    async deleteCar(id) {
        await axios.delete(`${API_BASE_URL}/cars/${id}`);
    }
}

export default new CarService();
