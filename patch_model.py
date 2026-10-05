with open('/Users/user/Work/masjid-system/app/Models/FinanceTransaction.php', 'r') as f:
    content = f.read()

# Add attachment to fillable
content = content.replace("'notes',", "'notes',\n        'attachment',")

# Add appends
if 'protected $appends' not in content:
    content = content.replace('protected function casts(): array', "protected $appends = ['attachment_url'];\n\n    public function getAttachmentUrlAttribute()\n    {\n        return $this->attachment ? asset('storage/' . $this->attachment) : null;\n    }\n\n    protected function casts(): array")

with open('/Users/user/Work/masjid-system/app/Models/FinanceTransaction.php', 'w') as f:
    f.write(content)
