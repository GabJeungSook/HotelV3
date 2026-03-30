# V4 Inventory & Stock Flow — Complete Example

How menu items are created, stock added, deducted on orders, and audited.

---

## Setup

```
Branch A — Kitchen service area
Menu Category: "Main Course" (service_area='kitchen')
```

---

## Flow 1: Admin Creates Menu Item + Initial Stock

```
Admin → Menu → Kitchen → Add Item

menu_items:
  id: 10
  branch_id: 1
  menu_category_id: 3 (Main Course)
  name: "Adobo"
  price: 180.00
  item_code: "KC-010"
  is_active: true

menu_inventories:
  menu_item_id: 10
  stock: 0  ← starts empty

Admin → Inventory → Adobo → Add Stock: 50 servings

menu_stock_logs:
  menu_item_id: 10
  type: 'stock_in'
  quantity: 50.00
  stock_before: 0.00
  stock_after: 50.00
  reason: 'manual_add'
  reference_type: NULL
  reference_id: NULL
  created_by: Admin Ate Joy

menu_inventories:
  stock: 0 → 50

activity_log (Spatie auto): MenuItem created, stock added
```

---

## Flow 2: Kitchen Staff Adds More Stock (Restock)

Morning delivery arrives. Kitchen adds 30 more servings.

```
menu_stock_logs:
  type: 'stock_in'
  quantity: 30.00
  stock_before: 50.00
  stock_after: 80.00
  reason: 'manual_add'
  created_by: Kitchen Juan

menu_inventories: stock 50 → 80
```

---

## Flow 3: Guest Orders Food — Stock Deducted

Guest in Room 101 orders Adobo x2.

```
transactions:
  #20  food  360.00  stay_id=500  "Food order (Kitchen)"

transaction_items:
  transaction_id=20  name="Adobo"  qty=2  unit_price=180.00  amount=360.00  menu_item_id=10

menu_stock_logs:
  menu_item_id: 10
  type: 'stock_out'
  quantity: 2.00
  stock_before: 80.00
  stock_after: 78.00
  reason: 'guest_order'
  reference_type: 'transaction'
  reference_id: 20
  created_by: Kitchen Juan

menu_inventories: stock 80 → 78
```

---

## Flow 4: Multiple Orders Throughout the Day

```
10:30 AM — Guest 101 orders Adobo x2     → stock 78 → 76
11:00 AM — Guest 205 orders Adobo x1     → stock 76 → 75
12:00 PM — Guest 301 orders Adobo x3     → stock 75 → 72
 1:00 PM — Guest 101 orders Adobo x1     → stock 72 → 71
 2:00 PM — Kitchen restocks +20          → stock 71 → 91
 3:00 PM — Guest 205 orders Adobo x2     → stock 91 → 89
```

Each creates a `menu_stock_logs` entry with before/after.

---

## Flow 5: Spoilage / Wastage

End of day — 5 servings spoiled, must be written off.

```
menu_stock_logs:
  type: 'stock_out'
  quantity: 5.00
  stock_before: 89.00
  stock_after: 84.00
  reason: 'spoilage'
  reference_type: NULL
  reference_id: NULL
  created_by: Kitchen Juan

menu_inventories: stock 89 → 84
```

---

## Flow 6: Stock Adjustment (Correction)

Physical count shows 82 servings, but system says 84. Off by 2.

```
menu_stock_logs:
  type: 'stock_out'
  quantity: 2.00
  stock_before: 84.00
  stock_after: 82.00
  reason: 'adjustment'
  created_by: Admin Ate Joy

menu_inventories: stock 84 → 82
```

---

## Flow 7: Item Out of Stock

Guest orders Adobo but stock is 0.

```
System checks: menu_inventories WHERE menu_item_id = 10 → stock = 0

ERROR: "Adobo is out of stock"

Transaction NOT created. Stock NOT deducted.
Kitchen staff must restock before orders can continue.
```

---

## Flow 8: Void Restores Stock

Guest ordered Adobo x2 (₱360) but order was wrong — void it.

```
Original:
  #20  food  360.00  (Adobo x2)
  menu_stock_logs: stock_out 2, stock 80→78

Void:
  #21  void  360.00  linked_transaction_id=20  "Voided: wrong order"

  menu_stock_logs:
    type: 'stock_in'
    quantity: 2.00
    stock_before: 78.00
    stock_after: 80.00
    reason: 'adjustment'
    reference_type: 'transaction'
    reference_id: 21 (the void transaction)
    created_by: Maria

  menu_inventories: stock 78 → 80 (restored)
```

---

## Full Stock Log for Adobo (One Day)

```
┌──────────┬──────────┬──────┬────────┬────────┬──────────────┬──────────────┐
│ Time     │ Type     │ Qty  │ Before │ After  │ Reason       │ Who          │
├──────────┼──────────┼──────┼────────┼────────┼──────────────┼──────────────┤
│ 7:00 AM  │ stock_in │ +50  │ 0      │ 50     │ manual_add   │ Admin Joy    │
│ 8:00 AM  │ stock_in │ +30  │ 50     │ 80     │ manual_add   │ Kitchen Juan │
│ 10:30 AM │ stock_out│ -2   │ 80     │ 78     │ guest_order  │ Kitchen Juan │
│ 11:00 AM │ stock_out│ -1   │ 78     │ 77     │ guest_order  │ Kitchen Juan │
│ 12:00 PM │ stock_out│ -3   │ 77     │ 74     │ guest_order  │ Kitchen Juan │
│ 1:00 PM  │ stock_out│ -1   │ 74     │ 73     │ guest_order  │ Kitchen Juan │
│ 2:00 PM  │ stock_in │ +20  │ 73     │ 93     │ manual_add   │ Kitchen Juan │
│ 3:00 PM  │ stock_out│ -2   │ 93     │ 91     │ guest_order  │ Kitchen Juan │
│ 5:00 PM  │ stock_out│ -5   │ 91     │ 86     │ spoilage     │ Kitchen Juan │
│ 6:00 PM  │ stock_out│ -2   │ 86     │ 84     │ adjustment   │ Admin Joy    │
├──────────┴──────────┴──────┴────────┴────────┴──────────────┴──────────────┤
│ Opening: 0 │ In: +100 │ Out: -16 │ Spoilage: -5 │ Adjust: -2 │ Final: 84  │
└────────────────────────────────────────────────────────────────────────────┘
```

---

## Inventory Report Query

```sql
SELECT
  mi.name, mi.item_code, mi.price,
  mc.name as category,
  inv.stock as current_stock,

  COALESCE(SUM(CASE WHEN sl.type='stock_in' AND sl.reason='manual_add'
    THEN sl.quantity ELSE 0 END), 0) as total_restocked,

  COALESCE(SUM(CASE WHEN sl.type='stock_out' AND sl.reason='guest_order'
    THEN sl.quantity ELSE 0 END), 0) as total_sold,

  COALESCE(SUM(CASE WHEN sl.type='stock_out' AND sl.reason='spoilage'
    THEN sl.quantity ELSE 0 END), 0) as total_spoiled,

  COALESCE(SUM(CASE WHEN sl.type='stock_out' AND sl.reason='adjustment'
    THEN sl.quantity ELSE 0 END), 0) as total_adjusted

FROM menu_items mi
JOIN menu_categories mc ON mc.id = mi.menu_category_id
JOIN menu_inventories inv ON inv.menu_item_id = mi.id
LEFT JOIN menu_stock_logs sl ON sl.menu_item_id = mi.id
  AND sl.created_at BETWEEN :from AND :to
WHERE mi.branch_id = :branch_id
AND mc.service_area = :area
GROUP BY mi.id
```

---

## Stock Reasons

| Reason | Direction | When |
|--------|-----------|------|
| `manual_add` | stock_in | Admin/kitchen manually adds stock |
| `guest_order` | stock_out | Guest orders food/amenity/drink |
| `spoilage` | stock_out | Item expired or wasted |
| `adjustment` | stock_in or stock_out | Physical count correction or void restore |
