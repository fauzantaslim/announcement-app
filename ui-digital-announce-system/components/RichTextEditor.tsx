'use client'

import dynamic from 'next/dynamic'
import 'react-quill-new/dist/quill.snow.css'

// ⚠️ wajib dynamic import (biar aman dari SSR)
// @ts-ignore - react-quill-new doesn't have proper TS types but works fine
const ReactQuill = dynamic(() => import('react-quill-new'), { 
  ssr: false 
})

interface RichTextEditorProps {
  value: string
  onChange: (value: string) => void
  placeholder?: string
}

export default function RichTextEditor({ value, onChange, placeholder = 'Tulis pengumuman...' }: RichTextEditorProps) {
  const modules = {
    toolbar: [
      [{ header: [1, 2, 3, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link', 'image'],
      ['clean'],
    ],
  }

  return (
    <ReactQuill
      theme="snow"
      value={value}
      onChange={onChange}
      modules={modules}
      placeholder={placeholder}
    />
  )
}
