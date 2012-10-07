<!DOCTYPE html>
<html>
  <meta http-equiv='Content-Type' content='text/html; charset=utf8' />
  <head>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" ></script>
    <script src="js/main.js"></script>
    <link rel="stylesheet" href="css/main.css" />
    <style type="text/css">
    </style>
    <script>
      $(document).ready(function() {
        loadUsers();
        loadItems();
      })
    </script>
  </head>
  <body>
    <h1>Bufet v M-25 (Vyhlasuje sa súťaž na grafický dizajn!)</h1>
    <div class="people_pane" id="people">
      People
    </div>
    <div class="item_pane" id="items">
      Items
    </div>
    <div class="confirm_pane" id="confirm">
        <div class="amount" id="amount">
          Množstvo: <span id="amount_val" ></span> <span id="amount_type" ></span> <input type="submit" value="+" onclick="increaseAmount()" /> <input type="submit" value="-" onclick="decreaseAmount()" />

        </div>
        <div class="price" id="price">
          Cena za <span id="amount_type" ></span>: <span id="unit_price" ></span><br/>
          Celková cena: <span id="total_price"></span>
        </div>
        <div class="confirm" id="submit">
          <input type="submit" value="Kúp" onclick="submitOrder()" />
        </div>
        <div class="confirm" id="balance">
          Bilancia
        </div>
        <div class="confirm" id="payment">
          
          Chcem zaplatiť do kasičky <input type="number" value="0" id="payment_input"/> €
          <input type="submit" value="Zaplať" onclick="submitPayment()"/>
        </div>
        <div class="history" id="history">
         História
       </div>
    </div>
  </body>
</html>
