using System.Diagnostics;
using Microsoft.AspNetCore.Mvc;
using BookstoreCustomer.Models;
using BookstoreCustomer.Services;

namespace BookstoreCustomer.Controllers;

public class HomeController : Controller
{
    private readonly IBookService _bookService;

    public HomeController(IBookService bookService)
    {
        _bookService = bookService;
    }

    public IActionResult Index()
    {
        var featuredBooks = _bookService.GetAllBooks().Take(6).ToList();
        return View(featuredBooks);
    }

    public IActionResult Privacy()
    {
        return View();
    }

    [ResponseCache(Duration = 0, Location = ResponseCacheLocation.None, NoStore = true)]
    public IActionResult Error()
    {
        return View(new ErrorViewModel { RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier });
    }
}
