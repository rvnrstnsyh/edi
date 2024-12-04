import axios from "axios";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";

import { Head, Link } from "@inertiajs/react";
import { useEffect, useState } from "react";

export default function PointOfSale() {
  const [items, setItems] = useState([]);
  const [cart, setCart] = useState([]);

  useEffect(() => {
    axios.get("/api/items", {
      headers: {
        "Authorization": "Bearer " + localStorage.getItem("access_token"),
      },
    }).then((response) => setItems(response.data));
  }, []);

  const handleAddToCart = (item, quantity) => {
    if (quantity <= 0) {
      alert("Quantity must be greater than 0");
      return;
    }

    if (quantity > item.current_stock) {
      alert("Not enough stock available");
      return;
    }

    const updatedItems = items.map((currentItem) => {
      if (currentItem.id === item.id) {
        return {
          ...currentItem,
          current_stock: currentItem.current_stock - quantity,
        };
      }
      return currentItem;
    });

    setItems(updatedItems);

    const existingItemIndex = cart.findIndex((cartItem) =>
      cartItem.id === item.id
    );
    if (existingItemIndex > -1) {
      const updatedCart = [...cart];
      updatedCart[existingItemIndex].quantity += quantity;
      setCart(updatedCart);
    } else {
      setCart([...cart, { ...item, quantity }]);
    }
  };

  const handleRemoveFromCart = (itemId) => {
    const cartItem = cart.find((item) => item.id === itemId);
    const updatedItems = items.map((currentItem) => {
      if (currentItem.id === cartItem.id) {
        return {
          ...currentItem,
          current_stock: currentItem.current_stock + cartItem.quantity,
        };
      }
      return currentItem;
    });

    setItems(updatedItems);
    setCart(cart.filter((cartItem) => cartItem.id !== itemId));
  };

  const calculateTotal = () => {
    return cart.reduce(
      (total, cartItem) => total + (cartItem.price * cartItem.quantity),
      0,
    );
  };

  const handleCheckout = async () => {
    try {
      let allRequestsSuccessful = true;
      for (let item of cart) {
        const payload = {
          item_id: item.id,
          quantity: item.quantity,
        };
        const response = await axios.post("/api/pos/transactions", payload, {
          headers: {
            "Authorization": "Bearer " + localStorage.getItem("access_token"),
            "Content-Type": "application/json",
          },
        });
        if (response.status !== 201) {
          allRequestsSuccessful = false;
          break;
        }
      }
      if (allRequestsSuccessful) window.location.reload();
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Point of Sale
        </h2>
      }
    >
      <Head title="Point of Sale" />
      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900">
              {/* Cart Section */}
              <div>
                <h3 className="text-xl font-semibold">Cart</h3>
                <div className="mt-4">
                  {cart.length > 0
                    ? (
                      <ul className="space-y-4">
                        {cart.map((cartItem) => (
                          <li
                            key={cartItem.id}
                            className="flex justify-between items-center bg-gray-100 p-4 rounded-lg shadow-sm"
                          >
                            <span>
                              {cartItem.name} x{cartItem.quantity} (Rp.{" "}
                              {cartItem.price * cartItem.quantity})
                            </span>
                            <button
                              onClick={() => handleRemoveFromCart(cartItem.id)}
                              className="text-red-500 hover:underline"
                            >
                              Remove
                            </button>
                          </li>
                        ))}
                      </ul>
                    )
                    : <p className="text-gray-500">Your cart is empty.</p>}
                </div>
              </div>

              {/* Total Price Section */}
              {cart.length > 0 && (
                <div className="mt-6 flex justify-between items-center">
                  <span className="text-xl font-semibold">Total:</span>
                  <span className="text-xl font-bold text-gray-900">
                    Rp. {calculateTotal().toLocaleString("id-ID", {
                      minimumFractionDigits: 2,
                      maximumFractionDigits: 2,
                    })}
                  </span>
                </div>
              )}

              {/* Checkout Button */}
              {cart.length > 0 && (
                <div className="mt-6 flex justify-end">
                  <button
                    onClick={handleCheckout}
                    className="w-full text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition duration-300 ease-in-out"
                  >
                    Checkout
                  </button>
                </div>
              )}

              {/* Items Section */}
              <div className="mt-5 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                {items.map((item) => (
                  <div
                    key={item.id}
                    className="w-full max-w-sm mx-auto bg-white border border-gray-200 rounded-lg shadow-md"
                  >
                    <div className="relative">
                      <img
                        className="p-8 rounded-t-lg w-full h-52 object-cover"
                        src={item.image_url || "/placeholder-image.png"}
                        alt={item.name}
                        onError={(e) => {
                          e.target.src = "/placeholder-image.png";
                          e.target.onerror = null;
                        }}
                      />
                      {item.current_stock <= 10 && (
                        <span className="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-2.5 py-0.5 rounded">
                          Low Stock
                        </span>
                      )}
                    </div>
                    <div className="px-5 pb-5">
                      <h5 className="text-lg font-semibold tracking-tight text-gray-900 h-16 overflow-hidden">
                        {item.name}
                      </h5>
                      <hr className="border-t-2 border-gray-200" />
                      <div className="flex items-center justify-between mt-4">
                        <span className="text-2xl font-bold text-gray-900">
                          <span className="text-sm">Rp.</span>
                          {item.price.toLocaleString("id-ID", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2,
                          })}
                        </span>
                        <div className="flex items-center space-x-2">
                          <span className="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                            {item.current_stock}
                          </span>
                        </div>
                      </div>
                      <div className="mt-4 flex space-x-2">
                        <input
                          type="number"
                          id={`quantity-${item.id}`}
                          className="block w-full h-10 lg:h-12 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                          required
                          min="1"
                          placeholder="Qty"
                        />
                        <button
                          onClick={() => {
                            const quantity = parseInt(
                              document.getElementById(`quantity-${item.id}`)
                                .value,
                            );
                            handleAddToCart(item, quantity);
                          }}
                          className="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg text-sm md:text-xs px-5 py-2.5 text-center transition duration-300 ease-in-out"
                        >
                          Add to Cart
                        </button>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
              {items.length === 0 && (
                <div className="text-center py-10 text-gray-500">
                  No items found
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
