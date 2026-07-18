# Organization Module

Sprint 3 adds UUID-based companies, branches, warehouse types, warehouses, sales channels, and organizational access. Every operational record is scoped by `company_id`; the seed data creates only the Anil company (`ANIL`) and related Persian organizational records without hard-coded IDs.

Sales channels are intentionally separate from warehouses. Website, Instagram, Bale, exhibitions, wholesale, organizational, and consignment sales are channels that may require a warehouse selection later, but they do not hold inventory quantities in this sprint.

Branch deletion is blocked when active warehouses exist. External branches, such as Passdaran, are flagged as non-operational for shared warehouse flows by default.
