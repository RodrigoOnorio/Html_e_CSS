"use client"

import type React from "react"
import { useState, useRef, useEffect } from "react"
import { Button } from "@/components/ui/button"

interface EmojiPickerButtonProps {
  onEmojiSelect: (emoji: string) => void
  onFileSelect: (file: File) => void
  isDark: boolean
}

const EMOJIS = [
  "😀",
  "😃",
  "😄",
  "😁",
  "😆",
  "😅",
  "🤣",
  "😂",
  "😊",
  "😇",
  "🙂",
  "🙃",
  "😉",
  "😌",
  "😍",
  "🥰",
  "😘",
  "😗",
  "😚",
  "😙",
  "🥲",
  "😋",
  "😛",
  "😜",
  "🤪",
  "😝",
  "🤑",
  "🤗",
  "🤭",
  "🤫",
  "🤔",
  "🤐",
  "🤨",
  "😐",
  "😑",
  "😶",
  "😏",
  "😒",
  "🙄",
  "😬",
  "🤥",
  "😌",
  "😔",
  "😪",
  "🤤",
  "😴",
  "😷",
  "🤒",
  "🤕",
  "🤢",
  "🤮",
  "🤧",
  "🤬",
  "🤡",
  "😈",
  "👿",
  "💀",
  "☠️",
  "💩",
  "🤓",
  "😎",
  "🤩",
  "🥳",
]

export default function EmojiPickerButton({ onEmojiSelect, onFileSelect, isDark }: EmojiPickerButtonProps) {
  const [showPicker, setShowPicker] = useState(false)
  const [uploading, setUploading] = useState(false)
  const pickerRef = useRef<HTMLDivElement>(null)
  const fileInputRef = useRef<HTMLInputElement>(null)

  useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (pickerRef.current && !pickerRef.current.contains(event.target as Node)) {
        setShowPicker(false)
      }
    }

    if (showPicker) {
      document.addEventListener("mousedown", handleClickOutside)
      return () => document.removeEventListener("mousedown", handleClickOutside)
    }
  }, [showPicker])

  const handleEmojiClick = (emoji: string) => {
    onEmojiSelect(emoji)
    setShowPicker(false)
  }

  const handleFileClick = () => {
    fileInputRef.current?.click()
  }

  const handleFileChange = async (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0]
    if (!file) return

    setUploading(true)
    try {
      const formData = new FormData()
      formData.append("file", file)
      formData.append("userId", localStorage.getItem("userId") || "")

      const response = await fetch("http://localhost/tentativa2/api/upload.php", {
        method: "POST",
        body: formData,
      })

      const data = await response.json()
      if (data.success) {
        onFileSelect(file)
        // Enviar mensagem com arquivo para o chat
        const fileData = {
          type: data.fileType || 'file',
          fileUrl: data.fileUrl,
          fileName: data.fileName,
          fileSize: data.fileSize,
          mimeType: data.mimeType
        }
        // Notificar o componente pai sobre o arquivo
        onFileSelect(fileData as any)
      } else {
        console.error("[v0] Upload error:", data.message)
      }
    } catch (err) {
      console.error("[v0] File upload error:", err)
    } finally {
      setUploading(false)
      if (fileInputRef.current) {
        fileInputRef.current.value = ""
      }
    }
  }

  return (
    <div className="relative flex gap-1" ref={pickerRef}>
      <Button
        type="button"
        variant="ghost"
        size="sm"
        onClick={() => setShowPicker(!showPicker)}
        className={`rounded-full w-10 h-10 p-0 text-lg flex items-center justify-center ${
          isDark ? "bg-slate-700 hover:bg-slate-600 text-white" : "hover:bg-gray-100"
        }`}
      >
        {showPicker ? "✕" : "😀"}
      </Button>

      <Button
        type="button"
        variant="ghost"
        size="sm"
        onClick={handleFileClick}
        disabled={uploading}
        className={`rounded-full w-10 h-10 p-0 flex items-center justify-center ${
          isDark ? "bg-slate-700 hover:bg-slate-600 text-blue-400" : "hover:bg-gray-100 text-blue-600"
        } ${uploading ? "opacity-50 cursor-not-allowed" : ""}`}
      >
        <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
          <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-1 9h-5v5h-2v-5H7v-2h5V8h2v5h5v2z" />
        </svg>
      </Button>

      <input
        ref={fileInputRef}
        type="file"
        onChange={handleFileChange}
        className="hidden"
        accept="image/*,.pdf,.doc,.docx,.txt"
        disabled={uploading}
      />

      {showPicker && (
        <div
          className={`absolute bottom-12 left-0 border rounded-lg shadow-lg p-3 z-50 ${
            isDark ? "bg-slate-800 border-slate-700" : "bg-white border-gray-200"
          }`}
        >
          <div className="grid grid-cols-8 gap-2" style={{ width: "280px", maxHeight: "250px", overflowY: "auto" }}>
            {EMOJIS.map((emoji) => (
              <button
                key={emoji}
                type="button"
                onClick={() => {
                  onEmojiSelect(emoji)
                  setShowPicker(false)
                }}
                className="text-xl hover:scale-125 transition-transform cursor-pointer hover:bg-blue-200 dark:hover:bg-slate-700 p-2 rounded flex items-center justify-center"
              >
                {emoji}
              </button>
            ))}
          </div>
        </div>
      )}
    </div>
  )
}
