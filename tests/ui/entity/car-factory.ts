import { faker } from '@faker-js/faker';

faker.seed(123);

export type Car = {
  make: string;
  model: string;
  year: number;
  price: number;
};

export class CarFactory {
  private makes = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Audi', 'Volkswagen', 'Nissan', 'Hyundai'];
  private models = ['Model S', 'Civic', 'F-150', 'Malibu', '3 Series', 'C-Class', 'A4', 'Golf', 'Altima', 'Elantra'];

  create(): Car {
    return {
      make: faker.helpers.arrayElement(this.makes),
      model: faker.helpers.arrayElement(this.models),
      year: faker.number.int({ min: 2000, max: 2025 }),
      price: faker.number.int({ min: 15000, max: 100000 }),
    };
  }

  createMany(count: number): Car[] {
    const cars: Car[] = [];
    const usedKeys = new Set<string>(); // для уникальных комбинаций make+model

    while (cars.length < count) {
      const car = this.create();
      const key = `${car.make}-${car.model}`;

    // если комбинация ещё не встречалась, добавляем
      if (!usedKeys.has(key)) {
        usedKeys.add(key);
        cars.push(car);
      }
    }

    return cars;
  }
}
