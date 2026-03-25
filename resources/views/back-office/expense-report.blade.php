<x-frontdesk-layout>
   <div class="mx-auto px-20 ">
    <h1 class="font-bold text-2xl text-gray-500">Expense Report</h1>
    <div class="mt-5">
          <livewire:back-office.expense-report />
    </div>
  </div>

    <script>
    function printOut(data) {
      var mywindow = window.open('', '', 'height=1000,width=1000');
      mywindow.document.write('<html><head>');
      mywindow.document.write('<title></title>');
      mywindow.document.write(`<link rel="stylesheet" href="{{ Vite::asset('resources/css/app.css') }}" />`);
      mywindow.document.write('</head><body >');
      mywindow.document.write(data);
      mywindow.document.write('</body></html>');

      mywindow.document.close();
      mywindow.focus();
      setTimeout(() => {
        mywindow.print();
        return true;
      }, 1000)


    }
  </script>
</x-frontdesk-layout>
