# Database Guide

Dokumen ini adalah referensi struktur database MasjidOS. AI agent harus menyesuaikan dengan convention existing project.

## Naming Rule

- Gunakan plural table name jika project convention begitu.
- Gunakan snake_case untuk kolom.
- Gunakan foreign key eksplisit jika framework mendukung.
- Tambahkan timestamps sesuai convention.
- Gunakan soft delete jika project sudah memakai soft delete.

## Core Tables MVP

### mosques

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| name | string | required |
| address | text | nullable |
| phone | string | nullable |
| email | string | nullable |
| logo_path | string | nullable |
| description | text | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### roles

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| name | string | e.g. Super Admin |
| slug | string | e.g. super_admin |
| description | text | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### finance_categories

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable for future multi mosque |
| name | string | required |
| type | string/enum | income/expense |
| description | text | nullable |
| is_active | boolean | default true |
| created_at | timestamp |  |
| updated_at | timestamp |  |

Default income categories:

- Infaq Kotak Amal
- Donasi Transfer
- Donasi QRIS
- Zakat
- Wakaf
- Sponsorship Kegiatan
- Sewa Fasilitas
- Lainnya

Default expense categories:

- Gaji Marbot
- Listrik
- Air
- Internet
- Konsumsi Kajian
- Perawatan Masjid
- Santunan
- Pembelian Aset
- Kegiatan Ramadan
- Qurban
- Lainnya

### financial_transactions

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| category_id | FK | required |
| type | string/enum | income/expense |
| amount | decimal/integer | required, > 0 |
| transaction_date | date | required |
| title | string | required |
| description | text | nullable |
| payment_method | string | cash/transfer/qris/other |
| proof_path | string | nullable |
| status | string/enum | draft/pending/approved/rejected |
| created_by | FK users | required if auth available |
| approved_by | FK users | nullable |
| approved_at | timestamp | nullable |
| rejected_reason | text | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

Index suggestion:

- `transaction_date`
- `type`
- `status`
- `category_id`
- `mosque_id`

### donation_categories

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| name | string | required |
| description | text | nullable |
| is_active | boolean | default true |
| created_at | timestamp |  |
| updated_at | timestamp |  |

Default categories:

- Infaq
- Zakat
- Wakaf
- Pembangunan
- Sosial
- Operasional Masjid
- Kajian
- Ramadan
- Qurban

### donations

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| jamaah_id | FK | nullable |
| category_id | FK | required |
| donor_name | string | nullable if anonymous allowed |
| donor_phone | string | nullable |
| donor_email | string | nullable |
| amount | decimal/integer | required, > 0 |
| donation_date | date | required |
| payment_method | string | nullable |
| proof_path | string | nullable |
| message | text | nullable |
| is_anonymous | boolean | default false |
| status | string/enum | pending/confirmed/rejected |
| confirmed_by | FK users | nullable |
| confirmed_at | timestamp | nullable |
| financial_transaction_id | FK | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### schedules

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| schedule_type | string/enum | shalat/imam/muadzin/khatib/bilal/kultum/kajian/marbot/other |
| title | string | required |
| date | date | required |
| start_time | time | nullable |
| end_time | time | nullable |
| person_name | string | nullable |
| person_phone | string | nullable |
| location | string | nullable |
| notes | text | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### events

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| title | string | required |
| slug | string | nullable |
| description | text | nullable |
| event_type | string | kajian/tpa/rapat/ramadan/santunan/qurban/other |
| speaker_name | string | nullable |
| location | string | nullable |
| start_datetime | datetime | required |
| end_datetime | datetime | nullable |
| poster_path | string | nullable |
| estimated_budget | decimal/integer | nullable |
| actual_budget | decimal/integer | nullable |
| status | string/enum | draft/published/completed/cancelled |
| created_by | FK users | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### jamaahs

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| name | string | required |
| phone | string | nullable |
| email | string | nullable |
| address | text | nullable |
| rt | string | nullable |
| rw | string | nullable |
| gender | string | nullable |
| birth_date | date | nullable |
| category | string | umum/pengurus/relawan/mustahik/donatur |
| notes | text | nullable |
| is_active | boolean | default true |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### assets

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| name | string | required |
| category | string | nullable |
| quantity | integer | default 1 |
| condition | string | baik/perlu_perbaikan/rusak/hilang/dipinjam |
| location | string | nullable |
| purchase_date | date | nullable |
| purchase_price | decimal/integer | nullable |
| photo_path | string | nullable |
| notes | text | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### documents

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| title | string | required |
| document_type | string | surat_masuk/surat_keluar/legal/sk_pengurus/proposal/lpj/template/other |
| document_number | string | nullable |
| document_date | date | nullable |
| file_path | string | nullable |
| description | text | nullable |
| created_by | FK users | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

### announcements

| Field | Type | Note |
|---|---|---|
| id | bigint/uuid | primary key |
| mosque_id | FK | nullable |
| title | string | required |
| content | text | required |
| category | string | nullable |
| publish_date | date/datetime | nullable |
| status | string/enum | draft/published/archived |
| is_public | boolean | default true |
| created_by | FK users | nullable |
| created_at | timestamp |  |
| updated_at | timestamp |  |

## Later Phase Tables

### zakat_records

- muzakki_name
- muzakki_phone
- zakat_type: fitrah/mal
- amount_money
- amount_rice
- payment_date
- status
- notes

### zakat_distributions

- mustahik_id
- amount_money
- amount_rice
- distribution_date
- status
- notes

### wakaf_records

- wakif_name
- wakif_phone
- wakaf_type
- amount
- asset_description
- purpose
- status
- usage_notes

### qurban_records

- participant_name
- participant_phone
- animal_type
- share_count
- group_code
- amount_due
- amount_paid
- payment_status
- slaughter_status
- distribution_status
- notes

### qurban_animals

- animal_type
- code
- weight
- price
- seller
- health_notes
- slaughter_status
- distribution_status

### audit_logs

- user_id
- action
- entity_type
- entity_id
- old_values
- new_values
- ip_address
- user_agent
- created_at

## Data Integrity Rules

- `financial_transactions.amount > 0`
- `donations.amount > 0`
- `assets.quantity >= 1`
- Finance category type must match transaction type.
- Only approved financial transactions affect official balance.
- Confirmed donation may create linked income transaction.
- Public views must not query sensitive fields directly.
