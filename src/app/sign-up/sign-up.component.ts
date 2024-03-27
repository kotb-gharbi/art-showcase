import { HttpClient  } from '@angular/common/http';
import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { RouterLink } from '@angular/router';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-sign-up',
  standalone: true,
  imports: [RouterLink,FormsModule],
  templateUrl: './sign-up.component.html',
  styleUrl: './sign-up.component.css'
})
export class SignUpComponent {
    

    constructor(private http : HttpClient) {}

    OnSignupSubmit(Signup : {Username : String , Email : String , Password : String , type : String}) {
      console.log(Signup);
      this.http.post("http://localhost/api/signup.php", Signup).subscribe((res) => {
    console.log(res);
    Swal.fire({
      title: "Good job!",
      text: "User created successfully!",
      icon: "success"
    });
    },
    (error) => {
    console.error("Error:", error);
    });}
  
}
