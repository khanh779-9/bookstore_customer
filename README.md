# BookStore Customer Website

An ASP.NET Core MVC web application for a bookstore customer portal where users can browse and view books.

## Features

- Browse all books in the collection
- Filter books by category (Fiction, Science Fiction, Fantasy, Romance, Mystery)
- Search books by title, author, or description
- View detailed information about each book including:
  - Title, Author, Description
  - Price, ISBN, Published Date
  - Stock availability
  - Category

## Technologies Used

- ASP.NET Core 10.0
- MVC Pattern
- Bootstrap 5 for responsive UI
- C# 13

## Prerequisites

- .NET 10.0 SDK or later

## Getting Started

1. Clone the repository:
```bash
git clone https://github.com/khanh779-9/bookstore_customer.git
cd bookstore_customer
```

2. Build the application:
```bash
dotnet build
```

3. Run the application:
```bash
dotnet run
```

4. Open your browser and navigate to:
```
https://localhost:5001
```
or
```
http://localhost:5000
```

## Project Structure

- **Controllers/** - MVC Controllers (Home, Books)
- **Models/** - Data models (Book, Category, ShoppingCartItem)
- **Services/** - Business logic (BookService)
- **Views/** - Razor views for UI
  - **Home/** - Homepage with featured books
  - **Books/** - Book listing and details
  - **Shared/** - Shared layout and components
- **wwwroot/** - Static files (CSS, JS, images)

## Current Book Collection

The application includes 10 sample books across various categories:
- Fiction: The Great Gatsby, To Kill a Mockingbird, The Catcher in the Rye, The Alchemist
- Science Fiction: 1984, The Hunger Games
- Fantasy: The Hobbit, Harry Potter and the Sorcerer's Stone
- Romance: Pride and Prejudice
- Mystery: The Da Vinci Code

## Future Enhancements

- Shopping cart functionality
- User authentication and profiles
- Order management
- Book reviews and ratings
- Admin panel for managing books
- Payment integration

## License

This project is for educational purposes.
