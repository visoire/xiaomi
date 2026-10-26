import tkinter as tk
from tkinter import ttk, messagebox, scrolledtext
import serial
import serial.tools.list_ports
import time
import threading
from PIL import Image, ImageTk
import os

class DebugWindow:
    def __init__(self, parent, port_var):
        self.parent = parent
        self.port_var = port_var
        self.is_monitoring = False
        self.serial_port = None
        
        # Create debug window
        self.window = tk.Toplevel(parent)
        self.window.title("Debug - COM Port Monitor")
        self.window.geometry("800x600")
        
        # Main frame
        main_frame = ttk.Frame(self.window, padding="10")
        main_frame.pack(fill=tk.BOTH, expand=True)
        
        # Control frame
        control_frame = ttk.Frame(main_frame)
        control_frame.pack(fill=tk.X, pady=(0, 10))
        
        ttk.Label(control_frame, text="Monitoring:").pack(side=tk.LEFT, padx=(0, 5))
        self.port_label = ttk.Label(control_frame, text=self.port_var.get(), font=("Arial", 10, "bold"))
        self.port_label.pack(side=tk.LEFT, padx=(0, 20))
        
        self.start_button = ttk.Button(control_frame, text="Start Monitoring", command=self.toggle_monitoring)
        self.start_button.pack(side=tk.LEFT, padx=(0, 10))
        
        ttk.Button(control_frame, text="Clear", command=self.clear_log).pack(side=tk.LEFT)
        
        # Status label
        self.status_label = ttk.Label(control_frame, text="● Stopped", foreground="red")
        self.status_label.pack(side=tk.RIGHT)
        
        # Log frames
        log_container = ttk.Frame(main_frame)
        log_container.pack(fill=tk.BOTH, expand=True)
        
        # TX Log
        tx_frame = ttk.LabelFrame(log_container, text="TX (Sent Data)", padding="5")
        tx_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True, padx=(0, 5))
        
        self.tx_log = scrolledtext.ScrolledText(tx_frame, height=25, width=40, bg="#f0f8ff")
        self.tx_log.pack(fill=tk.BOTH, expand=True)
        
        # RX Log
        rx_frame = ttk.LabelFrame(log_container, text="RX (Received Data)", padding="5")
        rx_frame.pack(side=tk.LEFT, fill=tk.BOTH, expand=True)
        
        self.rx_log = scrolledtext.ScrolledText(rx_frame, height=25, width=40, bg="#f8fff0")
        self.rx_log.pack(fill=tk.BOTH, expand=True)
        
        # Handle window close
        self.window.protocol("WM_DELETE_WINDOW", self.on_close)
        
    def toggle_monitoring(self):
        """Start or stop monitoring"""
        if not self.is_monitoring:
            self.start_monitoring()
        else:
            self.stop_monitoring()
            
    def start_monitoring(self):
        """Start monitoring the COM port"""
        port = self.port_var.get()
        if not port:
            messagebox.showerror("Error", "No COM port selected!")
            return
            
        try:
            self.serial_port = serial.Serial(
                port=port,
                baudrate=115200,
                bytesize=serial.EIGHTBITS,
                parity=serial.PARITY_NONE,
                stopbits=serial.STOPBITS_ONE,
                timeout=0.1
            )
            
            self.is_monitoring = True
            self.start_button.config(text="Stop Monitoring")
            self.status_label.config(text="● Monitoring", foreground="green")
            
            # Start monitoring thread
            self.monitor_thread = threading.Thread(target=self.monitor_port)
            self.monitor_thread.daemon = True
            self.monitor_thread.start()
            
            self.log_tx(f"Started monitoring {port} at 115200 baud\n")
            
        except Exception as e:
            messagebox.showerror("Error", f"Could not open port: {e}")
            
    def stop_monitoring(self):
        """Stop monitoring the COM port"""
        self.is_monitoring = False
        if self.serial_port and self.serial_port.is_open:
            self.serial_port.close()
        
        self.start_button.config(text="Start Monitoring")
        self.status_label.config(text="● Stopped", foreground="red")
        self.log_tx("Stopped monitoring\n")
        
    def monitor_port(self):
        """Monitor thread function"""
        buffer = bytearray()
        
        while self.is_monitoring:
            try:
                if self.serial_port and self.serial_port.is_open:
                    # Read available data
                    if self.serial_port.in_waiting > 0:
                        data = self.serial_port.read(self.serial_port.in_waiting)
                        buffer.extend(data)
                        
                        # Process buffer for 5A12 pattern
                        self.process_rx_buffer(buffer)
                        
                time.sleep(0.01)  # Small delay to prevent CPU overload
                
            except Exception as e:
                if self.is_monitoring:
                    self.log_rx(f"Error: {e}\n")
                break
                
    def process_rx_buffer(self, buffer):
        """Process received data and format with line breaks at 5A12"""
        hex_string = buffer.hex().upper()
        
        # Clear buffer after processing
        buffer.clear()
        
        # Split at 5A12 pattern
        parts = hex_string.split('5A12')
        
        formatted = ""
        for i, part in enumerate(parts):
            if i > 0:
                # Add 5A12 back at the start of each line (except first)
                formatted += "\n5A12"
            formatted += part
            
        # Add spaces between hex pairs for readability
        lines = formatted.split('\n')
        formatted_lines = []
        for line in lines:
            spaced = ' '.join([line[i:i+2] for i in range(0, len(line), 2)])
            formatted_lines.append(spaced)
            
        # Update RX log
        self.log_rx('\n'.join(formatted_lines) + '\n')
        
    def log_tx(self, message):
        """Log transmitted data"""
        self.tx_log.insert(tk.END, f"{time.strftime('%H:%M:%S')} - {message}")
        self.tx_log.see(tk.END)
        
    def log_rx(self, message):
        """Log received data"""
        self.rx_log.insert(tk.END, f"{time.strftime('%H:%M:%S')} - {message}")
        self.rx_log.see(tk.END)
        
    def send_data(self, hex_data):
        """Send data through the monitored port"""
        if self.is_monitoring and self.serial_port and self.serial_port.is_open:
            try:
                self.serial_port.write(hex_data)
                hex_string = hex_data.hex().upper()
                spaced = ' '.join([hex_string[i:i+2] for i in range(0, len(hex_string), 2)])
                self.log_tx(f"Sent: {spaced}\n")
            except Exception as e:
                self.log_tx(f"Send error: {e}\n")
                
    def clear_log(self):
        """Clear both logs"""
        self.tx_log.delete(1.0, tk.END)
        self.rx_log.delete(1.0, tk.END)
        
    def on_close(self):
        """Handle window close"""
        self.stop_monitoring()
        self.window.destroy()

class XiaomiPatcherGUI:
    def __init__(self, root):
        self.root = root
        self.root.title("Xiaomi 4 Lite Gen2 Patcher")
        self.root.geometry("600x550")
        self.root.resizable(False, False)
        
        # Available HEX data
        self.hex_data_options = {
            "EU": "5A 12 97 13 30 35 33 39 33 37 30 30 30 30 30 30 30 4A 55 50 4F 4D 41 24 1A",
            "DE": "5A 12 97 13 30 35 33 37 37 37 30 30 30 30 30 30 30 4A 55 50 4F 4D 41 6E 96"
        }
        
        self.selected_region = None
        self.is_sending = False
        self.debug_window = None
        
        # Load images
        self.load_images()
        
        self.setup_ui()
        self.refresh_ports()
        
        # Show disclaimer after everything is ready
        self.root.after(200, self.show_disclaimer)
        
    def load_images(self):
        """Load images and prepare for tkinter"""
        self.images = {}
        
        # Get the directory where the script is located
        script_dir = os.path.dirname(os.path.abspath(__file__))
        
        # Build full paths to image files
        image_paths = {
            'eu': os.path.join(script_dir, "EU.png"),
            'de': os.path.join(script_dir, "DE.png")
        }
        
        # Check which files exist
        missing_files = []
        for name, path in image_paths.items():
            if not os.path.exists(path):
                missing_files.append(os.path.basename(path))
        
        if missing_files:
            error_msg = f"Missing required image files in script directory:\n{script_dir}\n\n"
            for file in missing_files:
                error_msg += f"✗ {file}\n"
            error_msg += f"\nFound PNG files in script directory:\n"
            
            # List all PNG files in script directory
            try:
                png_files = [f for f in os.listdir(script_dir) if f.lower().endswith('.png')]
                if png_files:
                    for file in png_files:
                        error_msg += f"• {file}\n"
                else:
                    error_msg += "• No PNG files found\n"
            except:
                error_msg += "• Could not list directory contents\n"
                
            error_msg += f"\nPlease place all required files in the same folder as the Python script."
            
            messagebox.showerror("Missing Image Files", error_msg)
            return False
        
        try:

                
            # Load EU flag
            eu_img = Image.open(image_paths['eu'])
            eu_img = eu_img.resize((120, 80), Image.Resampling.LANCZOS)
            self.images['eu'] = ImageTk.PhotoImage(eu_img)
                
            # Load DE flag
            de_img = Image.open(image_paths['de'])
            de_img = de_img.resize((120, 80), Image.Resampling.LANCZOS)
            self.images['de'] = ImageTk.PhotoImage(de_img)
            
            print(f"✓ All images loaded successfully from: {script_dir}")
                
        except Exception as e:
            print(f"Error loading images: {e}")
            messagebox.showerror("Error Loading Images", f"Could not load image files from:\n{script_dir}\n\nError: {e}\n\nPlease check that the files are valid PNG images.")
            return False
        
        return True
            
    def show_disclaimer(self):
        """Show disclaimer dialog"""
        disclaimer_window = tk.Toplevel(self.root)
        disclaimer_window.title("Important Notice - Disclaimer")
        disclaimer_window.geometry("470x400")
        disclaimer_window.resizable(False, False)
        disclaimer_window.transient(self.root)
        disclaimer_window.grab_set()  # Make modal
        
        # Disclaimer text
        disclaimer_text = """Please note the following before proceeding:

This guide is provided for educational and research purposes only, to help you understand scooter systems, and is not intended for actual modifications. We do not endorse, promote, or encourage altering any scooter's firmware or hardware; any changes you make are entirely at your own risk. Be aware that any modification will void your manufacturer's warranty. You must comply with all applicable laws and regulations in your area, including vehicle modification rules, speed limits, road safety requirements, and intellectual property laws.

Modifying firmware or hardware may introduce serious safety hazards such as compromised brakes, unstable handling, electrical failures, fire risks, or personal injury. The authors make no warranties regarding the accuracy or completeness of this information and assume no liability for any damages, injuries, or losses that may result. By using this guide, you acknowledge and accept all risks, agree to comply with all legal requirements, and take full responsibility for your actions and their consequences. Proceed at your own discretion."""
        
        # Main container
        container = tk.Frame(disclaimer_window)
        container.pack(fill=tk.BOTH, expand=True, padx=15, pady=15)
        
        # Title
        title_label = tk.Label(container, text="⚠️ IMPORTANT NOTICE ⚠️", 
                              font=("Arial", 14, "bold"), fg="red")
        title_label.pack(pady=(0, 15))
        
        # Text area with smaller size
        text_widget = scrolledtext.ScrolledText(container, wrap=tk.WORD, 
                                               font=("Arial", 8), height=10, width=55)
        text_widget.pack(fill=tk.BOTH, expand=False, pady=(0, 15))
        text_widget.insert(1.0, disclaimer_text)
        text_widget.config(state=tk.DISABLED)
        
        # Button area - fixed at bottom
        button_container = tk.Frame(container)
        button_container.pack(fill=tk.X, side=tk.BOTTOM)
        
        # Countdown label
        self.countdown_label = tk.Label(button_container, text="Please wait 5 seconds...", 
                                       font=("Arial", 10), fg="gray")
        self.countdown_label.pack(pady=(0, 8))
        
        # OK button
        self.ok_button = ttk.Button(button_container, text="OK - I understand and accept", 
                                   command=lambda: self.close_disclaimer(disclaimer_window), state=tk.DISABLED)
        self.ok_button.pack(pady=(0, 8))
        
        # Start countdown
        self.countdown_seconds = 5
        self.update_countdown(disclaimer_window)
        
        # Center window
        disclaimer_window.update_idletasks()
        x = (disclaimer_window.winfo_screenwidth() // 2) - (disclaimer_window.winfo_width() // 2)
        y = (disclaimer_window.winfo_screenheight() // 2) - (disclaimer_window.winfo_height() // 2)
        disclaimer_window.geometry(f"+{x}+{y}")
        
    def close_disclaimer(self, disclaimer_window):
        """Close disclaimer and show main window"""
        disclaimer_window.destroy()
        self.root.deiconify()  # Show main window
        self.root.lift()  # Bring to front
        
    def update_countdown(self, window):
        """Update countdown for disclaimer"""
        if self.countdown_seconds > 0:
            self.countdown_label.config(text=f"Please wait {self.countdown_seconds} seconds...")
            self.countdown_seconds -= 1
            window.after(1000, lambda: self.update_countdown(window))
        else:
            self.countdown_label.config(text="You can now proceed:", fg="green")
            self.ok_button.config(state=tk.NORMAL)
        
    def setup_ui(self):
        # Main frame
        main_frame = ttk.Frame(self.root, padding="10")
        main_frame.grid(row=0, column=0, sticky=(tk.W, tk.E, tk.N, tk.S))
        

        
        # COM Port selection
        port_frame = ttk.LabelFrame(main_frame, text="COM Port Settings", padding="10")
        port_frame.grid(row=1, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        
        ttk.Label(port_frame, text="COM Port:").grid(row=0, column=0, sticky=tk.W, padx=(0, 10))
        
        self.port_var = tk.StringVar()
        self.port_combo = ttk.Combobox(port_frame, textvariable=self.port_var, state="readonly", width=15)
        self.port_combo.grid(row=0, column=1, sticky=tk.W, padx=(0, 10))
        
        ttk.Button(port_frame, text="Refresh", command=self.refresh_ports).grid(row=0, column=2, padx=(0, 10))
        
        # Debug button
        ttk.Button(port_frame, text="Debug", command=self.open_debug_window).grid(row=0, column=3, padx=(0, 10))
        
        # Fixed baudrate display
        ttk.Label(port_frame, text="Baudrate:").grid(row=1, column=0, sticky=tk.W, padx=(0, 10), pady=(10, 0))
        ttk.Label(port_frame, text="115200", font=("Arial", 10, "bold")).grid(row=1, column=1, sticky=tk.W, pady=(10, 0))
        
        # Region selection with flags
        region_frame = ttk.LabelFrame(main_frame, text="Click Region to Send Data", padding="20")
        region_frame.grid(row=2, column=0, columnspan=2, sticky=(tk.W, tk.E), pady=(0, 10))
        
        # Status label
        self.status_label = tk.Label(region_frame, text="Click a region button to send HEX data:", 
                                   font=("Arial", 11, "bold"), fg="blue")
        self.status_label.pack(pady=(0, 15))
        
        # Flag container
        flags_frame = tk.Frame(region_frame)
        flags_frame.pack()
        
        # EU flag button - will send data immediately when clicked
        self.eu_button = tk.Button(flags_frame, image=self.images['eu'], 
                                  width=120, height=80, bg="lightblue", relief="raised",
                                  command=lambda: self.select_and_send("EU"))
        self.eu_button.pack(side=tk.LEFT, padx=20)
        
        # DE flag button - will send data immediately when clicked
        self.de_button = tk.Button(flags_frame, image=self.images['de'], 
                                  width=120, height=80, bg="lightgray", relief="raised",
                                  command=lambda: self.select_and_send("DE"))
        self.de_button.pack(side=tk.LEFT, padx=20)
        
        # Log output
        log_frame = ttk.LabelFrame(main_frame, text="Log Output", padding="10")
        log_frame.grid(row=3, column=0, columnspan=2, sticky=(tk.W, tk.E, tk.N, tk.S))
        
        self.log_text = scrolledtext.ScrolledText(log_frame, height=12, width=70)
        self.log_text.pack(fill=tk.BOTH, expand=True)
        
        # Grid configuration
        main_frame.columnconfigure(0, weight=1)
        self.root.columnconfigure(0, weight=1)
        self.root.rowconfigure(0, weight=1)
        main_frame.rowconfigure(3, weight=1)
        
    def log_message(self, message):
        """Add message to log"""
        self.log_text.insert(tk.END, f"{time.strftime('%H:%M:%S')} - {message}\n")
        self.log_text.see(tk.END)
        self.root.update_idletasks()
        
    def refresh_ports(self):
        """Refresh available COM ports"""
        ports = serial.tools.list_ports.comports()
        port_names = [port.device for port in ports]
        port_names.sort(key=lambda x: int(x.replace('COM', '')) if x.startswith('COM') else 0, reverse=True)
        
        self.port_combo['values'] = port_names
        
        if port_names:
            # Set highest port as default
            self.port_var.set(port_names[0])
            self.log_message(f"Available ports: {', '.join(port_names)}")
            self.log_message(f"Default port set: {port_names[0]}")
        else:
            self.port_var.set("")
            self.log_message("No COM ports found!")
            
    def open_debug_window(self):
        """Open debug window for COM port monitoring"""
        if not self.port_var.get():
            messagebox.showerror("Error", "Please select a COM port first!")
            return
            
        if self.debug_window is None or not self.debug_window.window.winfo_exists():
            self.debug_window = DebugWindow(self.root, self.port_var)
        else:
            self.debug_window.window.lift()
            
    def select_and_send(self, region):
        """Select region and immediately send HEX data"""
        if self.is_sending:
            messagebox.showwarning("Busy", "Already sending data. Please wait...")
            return
            
        if not self.port_var.get():
            messagebox.showerror("Error", "Please select a COM port first!")
            return
            
        self.selected_region = region
        
        # Update UI to show selection
        self.eu_button.config(relief="raised", borderwidth=2)
        self.de_button.config(relief="raised", borderwidth=2)
        
        # Highlight selected button
        if region == "EU":
            self.eu_button.config(relief="sunken", borderwidth=4)
            self.status_label.config(text="Sending EU data...", fg="blue")
        elif region == "DE":
            self.de_button.config(relief="sunken", borderwidth=4)
            self.status_label.config(text="Sending DE data...", fg="red")
            
        self.log_message(f"Region {region} selected - sending data...")
        
        # Start sending data in separate thread
        self.send_data_thread()
            
    def send_data_thread(self):
        """Send HEX data in separate thread"""
        self.is_sending = True
        self.eu_button.config(state=tk.DISABLED)
        self.de_button.config(state=tk.DISABLED)
        
        thread = threading.Thread(target=self.send_hex_data)
        thread.daemon = True
        thread.start()
        
    def send_hex_data(self):
        """Send HEX data via serial port"""
        try:
            if not self.port_var.get():
                messagebox.showerror("Error", "Please select a COM port!")
                return
                
            if not self.selected_region:
                messagebox.showerror("Error", "Please select a region!")
                return
                
            # Get selected HEX data
            hex_string = self.hex_data_options[self.selected_region]
            
            # Convert HEX string to bytes
            hex_bytes = bytes.fromhex(hex_string.replace(" ", ""))
            
            # Check if debug window is open and monitoring
            if (self.debug_window and 
                hasattr(self.debug_window, 'window') and 
                self.debug_window.window.winfo_exists() and 
                self.debug_window.is_monitoring):
                # Send through debug window
                self.log_message(f"Sending via Debug Monitor...")
                self.debug_window.send_data(hex_bytes)
                self.log_message(f"✓ {len(hex_bytes)} bytes sent via Debug Monitor")
                
            else:
                # Send normally
                self.log_message(f"Connecting to {self.port_var.get()}...")
                
                # Open serial connection
                ser = serial.Serial(
                    port=self.port_var.get(),
                    baudrate=115200,
                    bytesize=serial.EIGHTBITS,
                    parity=serial.PARITY_NONE,
                    stopbits=serial.STOPBITS_ONE,
                    timeout=1
                )
                
                self.log_message(f"✓ Connected to {ser.port}")
                self.log_message(f"Sending HEX data ({self.selected_region}): {hex_string}")
                
                # Wait briefly for connection stabilization
                time.sleep(0.1)
                
                # Send data
                bytes_written = ser.write(hex_bytes)
                self.log_message(f"✓ {bytes_written} bytes sent")
                
                # Ensure all data is sent
                ser.flush()
                
                # Close connection
                ser.close()
                self.log_message("✓ Connection closed")
                
            self.log_message("=" * 50)
            
            # Update status
            self.status_label.config(text=f"✓ {self.selected_region} data sent successfully!", fg="green")
            
            messagebox.showinfo("Success", f"{self.selected_region} HEX data sent successfully!\n{len(hex_bytes)} bytes transferred.")
            
        except serial.SerialException as e:
            error_msg = f"Serial communication error: {e}"
            self.log_message(f"✗ {error_msg}")
            self.status_label.config(text="✗ Send failed!", fg="red")
            messagebox.showerror("Communication Error", error_msg)
        except Exception as e:
            error_msg = f"Unexpected error: {e}"
            self.log_message(f"✗ {error_msg}")
            self.status_label.config(text="✗ Send failed!", fg="red")
            messagebox.showerror("Error", error_msg)
        finally:
            self.is_sending = False
            self.eu_button.config(state=tk.NORMAL)
            self.de_button.config(state=tk.NORMAL)
            
            # Reset status label after 3 seconds
            self.root.after(3000, lambda: self.status_label.config(
                text="Click a region button to send HEX data:", fg="blue"))

def main():
    try:
        print("Starting Xiaomi Patcher GUI...")
        root = tk.Tk()
        app = XiaomiPatcherGUI(root)
        print("GUI initialized successfully")
        root.mainloop()
    except Exception as e:
        print(f"Error starting application: {e}")
        import traceback
        traceback.print_exc()
        input("Press Enter to exit...")

if __name__ == "__main__":
    main()

# Required dependencies:
# pip install pyserial pillow
#
# Required image files (must be in same folder):
# - EU.png (EU flag button)
# - DE.png (DE flag button)