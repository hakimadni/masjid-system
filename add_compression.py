with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'r') as f:
    content = f.read()

compression_logic = """const handleFileUpload = (e) => {
  const file = e.target.files[0]
  if (!file) return

  // Prevent compressing non-images (like PDF if allowed later)
  if (!file.type.startsWith('image/')) {
    createForm.attachment = file
    return
  }

  const reader = new FileReader()
  reader.readAsDataURL(file)
  reader.onload = (event) => {
    const img = new Image()
    img.src = event.target.result
    img.onload = () => {
      const canvas = document.createElement('canvas')
      const MAX_WIDTH = 1200
      const MAX_HEIGHT = 1200
      let width = img.width
      let height = img.height

      if (width > height) {
        if (width > MAX_WIDTH) {
          height = Math.round(height * (MAX_WIDTH / width))
          width = MAX_WIDTH
        }
      } else {
        if (height > MAX_HEIGHT) {
          width = Math.round(width * (MAX_HEIGHT / height))
          height = MAX_HEIGHT
        }
      }

      canvas.width = width
      canvas.height = height
      const ctx = canvas.getContext('2d')
      ctx.drawImage(img, 0, 0, width, height)

      canvas.toBlob((blob) => {
        if (!blob) return
        // Create a new compressed file
        const compressedFile = new File([blob], file.name, {
          type: 'image/jpeg',
          lastModified: Date.now()
        })
        
        // If compressed is somehow bigger than original, use original
        if (compressedFile.size > file.size) {
            createForm.attachment = file
        } else {
            createForm.attachment = compressedFile
        }
      }, 'image/jpeg', 0.7) // 70% quality
    }
  }
}"""

if 'const handleFileUpload' not in content:
    content = content.replace('const submitCreate = ()', compression_logic + '\n\nconst submitCreate = ()')

old_input = """<input type="file" @change="e => createForm.attachment = e.target.files[0]" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />"""
new_input = """<input type="file" @change="handleFileUpload" accept="image/*" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100" />"""

content = content.replace(old_input, new_input)

with open('/Users/user/Work/masjid-system/resources/js/Pages/Admin/Finance/Index.vue', 'w') as f:
    f.write(content)
