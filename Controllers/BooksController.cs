using Microsoft.AspNetCore.Mvc;
using BookstoreCustomer.Models;
using BookstoreCustomer.Services;

namespace BookstoreCustomer.Controllers
{
    public class BooksController : Controller
    {
        private readonly IBookService _bookService;

        public BooksController(IBookService bookService)
        {
            _bookService = bookService;
        }

        public IActionResult Index(string category, string search)
        {
            List<Book> books;

            if (!string.IsNullOrEmpty(search))
            {
                books = _bookService.SearchBooks(search);
                ViewBag.SearchTerm = search;
            }
            else if (!string.IsNullOrEmpty(category))
            {
                books = _bookService.GetBooksByCategory(category);
                ViewBag.Category = category;
            }
            else
            {
                books = _bookService.GetAllBooks();
            }

            ViewBag.Categories = _bookService.GetCategories();
            return View(books);
        }

        public IActionResult Details(int id)
        {
            var book = _bookService.GetBookById(id);
            if (book == null)
            {
                return NotFound();
            }
            return View(book);
        }
    }
}
