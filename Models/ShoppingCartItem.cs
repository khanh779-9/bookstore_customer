namespace BookstoreCustomer.Models
{
    public class ShoppingCartItem
    {
        public int Id { get; set; }
        public int BookId { get; set; }
        public Book? Book { get; set; }
        public int Quantity { get; set; }
        public string SessionId { get; set; } = string.Empty;
    }
}
