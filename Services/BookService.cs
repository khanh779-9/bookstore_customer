using BookstoreCustomer.Models;

namespace BookstoreCustomer.Services
{
    public interface IBookService
    {
        List<Book> GetAllBooks();
        Book? GetBookById(int id);
        List<Book> GetBooksByCategory(string category);
        List<string> GetCategories();
        List<Book> SearchBooks(string searchTerm);
    }

    public class BookService : IBookService
    {
        private readonly List<Book> _books;

        public BookService()
        {
            _books = new List<Book>
            {
                new Book
                {
                    Id = 1,
                    Title = "The Great Gatsby",
                    Author = "F. Scott Fitzgerald",
                    Description = "A classic American novel set in the Jazz Age, exploring themes of wealth, love, and the American Dream.",
                    Price = 12.99m,
                    Category = "Fiction",
                    ImageUrl = "/images/great-gatsby.jpg",
                    Stock = 25,
                    ISBN = "978-0743273565",
                    PublishedDate = new DateTime(1925, 4, 10)
                },
                new Book
                {
                    Id = 2,
                    Title = "To Kill a Mockingbird",
                    Author = "Harper Lee",
                    Description = "A gripping tale of racial injustice and childhood innocence in the American South.",
                    Price = 14.99m,
                    Category = "Fiction",
                    ImageUrl = "/images/mockingbird.jpg",
                    Stock = 30,
                    ISBN = "978-0061120084",
                    PublishedDate = new DateTime(1960, 7, 11)
                },
                new Book
                {
                    Id = 3,
                    Title = "1984",
                    Author = "George Orwell",
                    Description = "A dystopian social science fiction novel and cautionary tale about totalitarianism.",
                    Price = 13.99m,
                    Category = "Science Fiction",
                    ImageUrl = "/images/1984.jpg",
                    Stock = 20,
                    ISBN = "978-0451524935",
                    PublishedDate = new DateTime(1949, 6, 8)
                },
                new Book
                {
                    Id = 4,
                    Title = "Pride and Prejudice",
                    Author = "Jane Austen",
                    Description = "A romantic novel of manners that critiques the British landed gentry at the end of the 18th century.",
                    Price = 11.99m,
                    Category = "Romance",
                    ImageUrl = "/images/pride-prejudice.jpg",
                    Stock = 15,
                    ISBN = "978-0141439518",
                    PublishedDate = new DateTime(1813, 1, 28)
                },
                new Book
                {
                    Id = 5,
                    Title = "The Hobbit",
                    Author = "J.R.R. Tolkien",
                    Description = "A fantasy novel and children's book about the quest of hobbit Bilbo Baggins.",
                    Price = 15.99m,
                    Category = "Fantasy",
                    ImageUrl = "/images/hobbit.jpg",
                    Stock = 40,
                    ISBN = "978-0547928227",
                    PublishedDate = new DateTime(1937, 9, 21)
                },
                new Book
                {
                    Id = 6,
                    Title = "Harry Potter and the Sorcerer's Stone",
                    Author = "J.K. Rowling",
                    Description = "The first novel in the Harry Potter series, introducing the magical world of Hogwarts.",
                    Price = 16.99m,
                    Category = "Fantasy",
                    ImageUrl = "/images/harry-potter.jpg",
                    Stock = 50,
                    ISBN = "978-0590353427",
                    PublishedDate = new DateTime(1997, 6, 26)
                },
                new Book
                {
                    Id = 7,
                    Title = "The Catcher in the Rye",
                    Author = "J.D. Salinger",
                    Description = "A story about teenage rebellion and alienation, told through the eyes of Holden Caulfield.",
                    Price = 12.99m,
                    Category = "Fiction",
                    ImageUrl = "/images/catcher-rye.jpg",
                    Stock = 18,
                    ISBN = "978-0316769174",
                    PublishedDate = new DateTime(1951, 7, 16)
                },
                new Book
                {
                    Id = 8,
                    Title = "The Da Vinci Code",
                    Author = "Dan Brown",
                    Description = "A mystery thriller novel exploring religious conspiracy theories.",
                    Price = 14.99m,
                    Category = "Mystery",
                    ImageUrl = "/images/davinci-code.jpg",
                    Stock = 22,
                    ISBN = "978-0307474278",
                    PublishedDate = new DateTime(2003, 3, 18)
                },
                new Book
                {
                    Id = 9,
                    Title = "The Hunger Games",
                    Author = "Suzanne Collins",
                    Description = "A dystopian novel about a televised fight to the death in a post-apocalyptic nation.",
                    Price = 13.99m,
                    Category = "Science Fiction",
                    ImageUrl = "/images/hunger-games.jpg",
                    Stock = 35,
                    ISBN = "978-0439023481",
                    PublishedDate = new DateTime(2008, 9, 14)
                },
                new Book
                {
                    Id = 10,
                    Title = "The Alchemist",
                    Author = "Paulo Coelho",
                    Description = "A philosophical novel about following your dreams and finding your personal legend.",
                    Price = 14.99m,
                    Category = "Fiction",
                    ImageUrl = "/images/alchemist.jpg",
                    Stock = 28,
                    ISBN = "978-0062315007",
                    PublishedDate = new DateTime(1988, 1, 1)
                }
            };
        }

        public List<Book> GetAllBooks()
        {
            return _books;
        }

        public Book? GetBookById(int id)
        {
            return _books.FirstOrDefault(b => b.Id == id);
        }

        public List<Book> GetBooksByCategory(string category)
        {
            if (string.IsNullOrEmpty(category))
                return _books;
            
            return _books.Where(b => b.Category.Equals(category, StringComparison.OrdinalIgnoreCase)).ToList();
        }

        public List<string> GetCategories()
        {
            return _books.Select(b => b.Category).Distinct().OrderBy(c => c).ToList();
        }

        public List<Book> SearchBooks(string searchTerm)
        {
            if (string.IsNullOrEmpty(searchTerm))
                return _books;

            searchTerm = searchTerm.ToLower();
            return _books.Where(b => 
                b.Title.ToLower().Contains(searchTerm) ||
                b.Author.ToLower().Contains(searchTerm) ||
                b.Description.ToLower().Contains(searchTerm)
            ).ToList();
        }
    }
}
